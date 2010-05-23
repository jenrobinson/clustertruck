<?php

require_once "Twitter.class.php";
require_once "mdetect.class.php";

//////////////////////////////
/// FE
//////////////////////////////
abstract class Fe extends Framework {

	// default meta title
	public $metaTitle = 'Cluster Truck, Find Food Trucks in Realtime';
	
	// default meta description
	public $metaDescription = 'Get live tracking maps, menus, and schedules for LA-based food trucks.';

	// construct
	public function __construct($module=false) {
	
		// call our parent
		parent::__construct();
	
		// module
		$this->module = $module;	
		
		$this->truck = false;	
	
		// set our page urls in config
		Config::set('pages',array(
		
					// top
					'index' 	=> '',
					'home' 		=> '',
					'login' 	=> 'login?',
					'logout' 	=> 'logout?url='.urlencode(SELF),
					'join' 		=> 'user/join?',
					
					// twitter auth
					'twitter-auth'	=> 'user/twitter',
					
					// user profile
					'user-profile' 	=> 'user/profile/{uid}',
					'user-edit' 	=> 'user/edit',				
					
					// xhr
					'xhr' => 'xhr/',
					
				)
			);
			
		// try to get a session
		$this->getSession();	
			
		// context
		$this->context = p('_context','html');
		
		// twitter
		$this->twitter = new Twitter(TWITTER_USER,TWITTER_PASS);
		
		// m detect
		$this->uagent_info = new uagent_info();
		
		// t cookie
		$tcookie = p('CLT',false,$_COOKIE);	
		
		// expire
		$expire = time()+(60*60*24);
		
		setcookie('CLT',p('t',$tcookie),$expire,'/',COOKIE_DOMAIN,false,true);	
		
		$tcookie = p('CLT',false,$_COOKIE);	
		
		//redirect to remove the url if needed
		if (p('t') == 'standard') { 
			header('Location:'.URI); exit;
		} else if (p('t') == 'mobile') {
			header('Location:'.URI.'m'); exit;
		}
		
		// if smartphone, redirect to mobile url automatically
		if (p('class',false) != 'mobile' && $tcookie != 'standard' && $this->uagent_info->DetectSmartphone()) { 
					
			header('Location:'.URI.'m');
			
		}
	
	}
	
	// pull the tweets from the DB for posting
	public function getTweets() { 
	
			$tweets = $this->query("SELECT * FROM tweet_queue WHERE processed = 0 AND `expire` > ".strtotime('now')." ORDER BY id ASC LIMIT 30");
						
			return $tweets;		
	
	}
	
	// mark a tweet in the DB as tweeted
	public function markTweetProcessed($id) { 
	
			$tweet = $this->row("UPDATE tweet_queue SET processed = 1 WHERE `id`=? LIMIT 1",array($id));
						
			return $tweet;		
	
	}
	
	// get a truck row from the DB by it's slug
	public function getTruck($slug) { 
	
		if (!empty($slug)) { 
		
			$profile = $this->row("SELECT * FROM trucks WHERE slug = ? LIMIT 1",array($slug));
						
			return $profile;
		
		}
		
		return false;
	
	
	}
	
	// get a truck row from the DB by it's twitter ID
	public function getTruckByTwitter($twitter) { 
	
		if (!empty($twitter)) { 
		
			$profile = $this->row("SELECT * FROM trucks WHERE twitter = ? LIMIT 1",array($twitter));
						
			return $profile;
		
		}
		
		return false;
	
	
	}
	
	// get a truck row by it's id
	public function getTruckById($id) { 
	
		if (!empty($id)) { 
		
			$profile = $this->row("SELECT * FROM trucks WHERE id = ? LIMIT 1",array($id));
						
			return $profile;
		
		}
		
		return false;
	
	
	}
	
	// get all trucks from the db
	public function getTrucks($limit=100) { 
	
			$trucks = $this->query("SELECT * FROM trucks ORDER BY name ASC LIMIT ".$limit);
						
			return $trucks;
	
	
	}
	
	// get all areas from DB (for neighborhood list)
	public function getAreas($limit=100) { 
	
			$areas = $this->query("SELECT * FROM areas ORDER BY name ASC LIMIT ".$limit);
						
			return $areas;
	
	
	}
	
	// get area from db, for neighborhood page
	public function getArea($slug) { 
	
		if (!empty($slug)) { 
		
			$profile = $this->row("SELECT * FROM areas WHERE slug = ? LIMIT 1",array($slug));
						
			return $profile;
		
		}
		
		return false;
	
	
	}
	
	// get scheduled location from DB by id
	public function getScheduledLocation($id) { 
	
		if (!empty($id)) { 
		
			$loc = $this->row("SELECT * FROM locations WHERE id = ? LIMIT 1",array($id));
						
			return $loc;
		
		}
		
		return false;
	
	
	}
	
	// get last updated time, for homepage
	public function getLastUpdateTime() { 
			
			$when = $this->row("SELECT added_when FROM locations ORDER BY added_when DESC LIMIT 1");
									
			return $when['added_when'];
					
	
	}
	
	/* 
	// Get schedule for all, truck, or filtered by time
	// @truck_id = id of a truck to filter by
	// @filter = different types of filters (week,today,tomorrow,particular schedule id)
	*/
	
	public function getSchedule($truck_id=false,$filter=false) { 
	
			$now = strtotime('now');
			
			$bound = ''; 
			
			//echo 'filter:'.$filter;
			
			//check for filters
			if ($filter == 'week') { 
			
				$bound = ' AND time_start < ' . ($now + 604800); 
				
			} else if ($filter == 'today') { 
								
				$bound = ' AND time_start < ' . ( ($now + 86400) - ( ($now + 86400) - strtotime("tomorrow 12:00am") ) ) ; 
				
			} else if ($filter == 'tomorrow') { 
								
				$bound = ' AND time_start > ' . strtotime("tomorrow 12:00am") . ' AND time_start < ' . strtotime("tomorrow 11:59pm") ; 
				
			} else if (is_numeric($filter)) { 
								
				$bound = ' AND id = '.$filter; 
				
			}
			
	
			if ($truck_id) { 
							
				$trucks = $this->query("SELECT * FROM locations WHERE truck_id = ? AND time_stop > ? ".$bound." ORDER BY time_start ASC LIMIT 17",array($truck_id,$now));
				
			} else { 
			
				$trucks = $this->query("SELECT * FROM locations WHERE time_stop > ? ".$bound." ORDER BY time_start ASC LIMIT 17",array($now));
				
			}
				
				$schedule = array();
				
				$count = 0;
				
				foreach ($trucks as $s) { 
					
					$schedule[$count] = $s;				
					$schedule[$count]['datetime'] = date("D M j, g:ia",$s['time_start']).' - '.date("g:ia",$s['time_stop']);
					$truckInfo = $this->getTruckById($s['truck_id']);
					$schedule[$count]['truck_name'] = $truckInfo['name'];
					$schedule[$count]['address'] = str_replace("\n","",$s['address']);
										
					$count++;
					
				}
			
			
				
						
			return $schedule;
	
	
	}
	
	// shorten a URL with Bit.ly
	public function bitlyLink($link) {		
						
			// Set username and password
			$login = BITLY_LOGIN;
			$key = BITLY_KEY;
			
			$url = 'http://api.bit.ly/shorten?version=2.0.1&longUrl='.$link.'&login='.$login.'&apiKey='.$key;
			
			$curl_handle = curl_init();
			curl_setopt($curl_handle, CURLOPT_URL, "$url");
			curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl_handle, CURLOPT_POST, 1);
			$buffer = curl_exec($curl_handle);
			curl_close($curl_handle);
			// check for success or failure
			
			$result = json_decode($buffer); 
			
			foreach ($result->results as $result) { 
				
				$shortLink = $result->shortUrl;
				break;
			
			}
			
			if (!empty($shortLink)) { 
				return $shortLink;
			}
			
			return false;
	
	
	
	}
	
	
	////////////////////////////////////////////
	/// process an uploaded image and put it on amazon
	////////////////////////////////////////////
	public function processUploadedImage() {	
	
		// blank r
		$r = false;
				
		// name
		$name = 'uimage';
	
		// check for the 
		if ( $_FILES[$name]['error'] === UPLOAD_ERR_OK ) { 
		
			// file
			$file = $_FILES[$name];
		
			// get some info about the image
			list($w,$h,$t) = getimagesize($file['tmp_name']);
		
			// mime
			$mime = image_type_to_mime_type($t);
		
			// good type
			if ( $mime == 'image/jpeg' OR $mime == 'image/gif' OR $mime == 'image/png' ) {
			
				// need to rewrite as a valid image
				$img = @imagecreatefromstring( file_get_contents($file['tmp_name']) );
				
				// ext
				$ext = strtolower(array_pop( explode(".",$file['name']) ));
			
				// is it good
				if ( $img ) {
			
					// now that all that is done
					// lets create a tmp name
					$uploadfile = "/tmp/" . md5( uniqid() ). "." . $ext;
												
					// move it to tmp
					$check = move_uploaded_file($file['tmp_name'], $uploadfile);			
					
					// is good
					if ( $check ) {
						
						//put into amazon s3 bucket
						$this->s3->putObjectFile($uploadfile, S3_BUCKET, baseName($uploadfile), S3::ACL_PUBLIC_READ);			
					
						// remove
						@unlink($uploadfile);

						// make our r
						$r =  'http://'.S3_BUCKET.'.s3.amazonaws.com/' . basename($uploadfile); 
							
					}
					
				}
				
			}
		
			// remove the tmp image just incase it's still there
			@unlink($file['tmp_name']);
			
		}
		
	
		// 	return
		return $r;			
	
	}
	
	// important security function, checks if someone has access to edit a particular page
	public function hasEditAccess() { 
		
		// any twitter handle in gods will get access to all pages
		$gods = array('jenrobinson','rochers');
		
		if (!isset($_GET['asUser']) && $this->loged && (in_array($this->user['name'],$gods) || strtolower($this->user['name']) == strtolower($this->truck['twitter']))) { 
			return true;
		}
		
	}
	
	
	// utility for printing json to the page
	public function printJsonResponse($rsp) {
		header("Content-Type: text/javascript");
		exit( json_encode( array_merge(array('stat'=>1),$rsp) ) );
	}
	
	public function makeReturnToken($args) {
		return base64_encode( json_encode($args) );
	}
	
	public function parseReturnToken($token) {
		return json_decode( base64_decode(urldecode($token)),true);
	}	
	
	public function getReturnToken($default=false) {	
		if ( !$default ) { $default = array('do'=>'redi','url'=>SELF); } 
		return json_decode( base64_decode( urldecode(p('return',$this->makeReturnToken($default)))),true);
	}
	
}