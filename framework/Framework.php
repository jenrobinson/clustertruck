<?php

// require facebook and twitter
//require_once FRAMEWORK_ROOT.'fb/facebook.php'; 	
require_once(FRAMEWORK_ROOT."twitter/EpiCurl.php");
require_once(FRAMEWORK_ROOT."twitter/EpiOAuth.php");
require_once(FRAMEWORK_ROOT."twitter/EpiTwitter.php");

require_once(FRAMEWORK_ROOT."amazon/s3.class.php"); 

// something we always include
require_once(FRAMEWORK_ROOT."Database.php");

/////////////////////////
/// Framework
/////////////////////////	
abstract class Framework extends DatabaseMask  {
		
	// holders
	public $title = "";
	public $bodyClass = "";
	public $mem = false;
    public $dbh = false;
	public $uid = false;
	public $db = false;
	public $loged = false;
	public $user = false;
	
	/* __construct */
	public function __construct() {		
		
		// build our config
		$this->config = array(
			
			// dir
			'dir' => array(
				'tmp' => '/tmp/',				
				'tmpl' => ROOT . 'tmpl/'
			)			
			
		); 			
		
		// database
		$this->db = Database::singleton();		
		$this->cache = false;			
		//$this->cache = Cache::singleton();
		
		// s3
		$this->s3 = new S3(S3_ACCESS,S3_SECRET);

	}
	
	function __destruct() {
		
	}
	
	public function md5($str) {
		return md5("jf89pohij2;3'damiufj".$str."84$89adfaw349408 43a4 038w4r awef aweufh7ao38rhuanwk/ mef");
	}
	
	
	
	
	
	public function doLogin($twitter_id,$data,$encrypted=false) { 
	
		// password not encrupted
		if ( !$encrypted ) {
			$pass = md5(md5($pass));
		}
	
		// check it 
		$row = $this->row("SELECT * FROM users WHERE twitter_id = ? ",array((int)$twitter_id));
		
		// what up 
		if ( $row ) {
	
			// session
			$sid = $this->md5( uniqid() );
			
			// add ip to data
			$row['ip'] = IP;		
			
			// expire
			$expire = time()+(60*60*24*14);	
			
			$data = json_decode($data);
			
			//api stuff
			$data->api_key = $row['api_key'];	
			
			//api stuff
			$data->api_secret = $row['api_secret'];		
			
			//make back to json
			$data = json_encode($data);			
			
			// add their session to the cache
			//$this->cache->set($sid,$row,$expire,'sessions');
			
			// create our session
 			$this->query("INSERT INTO `sessions` SET `sid` = ?, `user` = ?, `data` = ?, `timestp` = ?, `expire` = ? ",array($sid,$row['id'],$data,time(),$expire));
 			
 			//get username from db
 			$twitter_data = json_decode($row['twitter_data'],true);

			// bcookie
			$bcookie = base64_encode(json_encode(array('u'=>$row['id'],'s'=>$sid,'i'=>IP,'e'=>$expire,'c'=>$this->md5($twitter_data['username']))));
				
			// set A+B cookie
			setcookie('CLA',$sid,$expire,'/',COOKIE_DOMAIN,false,true);
			setcookie('CLB',$bcookie,$expire,'/',COOKIE_DOMAIN,false,true);
		
			// good
			return $row;
		
		}
		
		error_log('could not login, even though there was an id');
		
		//there was some problem so we should remove them from the user db so that it will re-auth
		$this->query("DELETE FROM users WHERE twitter_id = ? LIMIT 1",array((int)$twitter_id));
		
		// nope
		return false;

	}	
	
	public function doLogout() {

		// no session
 		$this->query("DELETE FROM `sessions` WHERE sid = ? LIMIT 1 ",array($this->sid));

		// remove sid
		//$this->cache->delete('*',"user.{$this->uid}");		
		//$this->cache->delete($this->sid,'sessions');

		// expire
		$expire = time()+1;

		// no cookies
		setcookie('CLA',false,$expire,'/',COOKIE_DOMAIN,false,true);
		setcookie('CLB',false,$expire,'/',COOKIE_DOMAIN,false,true);	
	
	}	
	
	
	public function doCurl($url) { 
	
		// create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $response = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch); 
        
        return $response;
	
	}
	
	
	public function requireSession($url=false) {
	
		// get a url
		$url = ($url?$url:$this->url('self'));
	
		// what url
		$return = $this->makeReturnToken(array('do'=>'redi','url'=>$url));
	
		if ( !$this->loged ) {
			$this->go($this->url('login',array(),array('return'=>$return)));
		}
	
	}
	
	public function getSession() { 
	
		// get cookies
		$acookie = p('CLA',false,$_COOKIE);
		$bcookie = p('CLB',false,$_COOKIE);		
	
		// check it 
		if ( $acookie AND $bcookie ) {
			
			// decode the bcookie
			$b = json_decode(base64_decode($bcookie),true);
		
			// check sid, ip and not expired
			if ( $b['s'] == $acookie AND $b['e'] > time() ) {						
			
				// get the session
				// and double check with the database
 				$sess = $this->row("SELECT * FROM sessions as s WHERE s.sid = ?  AND s.user = ? ",array($b['s'],$b['u']));
 				
 				//print_r($sess);

				// get from cache
				//$sess = $this->cache->p_get($b['s'],'sessions');
			
				// session
				if ( $sess ) {
				
					// data
					$data = $sess;
					
					$twitter_data = json_decode($data['data'],true);
					
					$twitter_username = $twitter_data['username'];
										
					// ok this is our last check
					// just need to make sure the passwords are ok
					if ( $this->md5($twitter_username) == $b['c'] ) { 
					
						// loged is true
						$this->loged = true;
						
						// easier name
						$twitter = json_decode($data['data'],true);
						$this->user['name'] = $twitter['username'];
						
						// set user
						//$this->user = $data;
					
						// user id
						//$this->uid = $data['id'];
						
						// sid
						$this->sid = $b['s'];
						
						// twitter data
						/*if ( $data['twitter_data'] ) {
							$this->user['twitter_data'] = json_decode($data['twitter_data'],true);
						}*/
						
						// twitter
						$this->user['twitter'] = $data['data'];
						
						//print_r($data);
						
						$this->user['api_key'] = json_decode($data['data'])->api_key;
						$this->user['api_secret'] = json_decode($data['data'])->api_secret;
						
						// set user with config
						Config::set('user',$this->user);
						Config::set('sid',$this->sid);
					
					} else {
					
						//$this->doLogout();
					
					}
									
				}
			
			}
		
		}
	
	}		
	
	// url shortcut
	public function url($key,$data=false,$params=false) {
		return Config::url($key,$data,$params);
	}
	
	// send email
	public function sendEmail($args) {
	
		// check for from 
		if ( !isset($args['from']) ) {
			$args['from'] = "no-reply@tenforms.com";
		}
	
		// hd4
		$hdr = "From: {$args['from']}";
	
		// send
		return mail($args['to'],$args['subject'],$args['message'],$hdr);
	
	}
		
	/**
	 * does the actual page creation
	 * @method	build
	 */
	public function dispatch($act=false,$tmpl=false) {
		
		// act
		if ( !$act ) {
			$act = str_replace('-','',p('act','main'));
		}
	
		// exists
		if ( !method_exists($this,$act) ) {
			$act = 'main';
		}							
		
		// start ob
		ob_start(); ob_clean();

			// tmpl
			if ( $tmpl ) {
				include($tmpl);
			}
			else {
				call_user_func(array($this,$act));
			}
			
		// get
		$Body = ob_get_contents(); 
		
		// end
		ob_end_clean(); 
	
		// what context 
		if ( $this->context == 'xhr' ) {
		
		    // header
            header("Content-Type: text/javascript");
        
			// need to remove comments 
			list($body,$js) = $this->parseHtmlForXhr($Body);
					        
        
            // make it nice
            exit( json_encode( array( 
            	'stat' => '1', 
            	'html' => $body, 
            	'bootstrap' => array('c'=>$this->bodyClass,'js'=> $js,'t'=>$this->title) 
            )) );
            
        } else if ( $this->context == 'text' ) {
        	
        	// header
            header("Content-Type: text/plain");
            
            exit($Body);
        
        
        }
		
		if (p('class',false) == 'mobile') { 
		
			// include the header
			include( $this->tmpl('mobile') );
		
		
		} else { 
		
			// include the header
			include( $this->tmpl('page') );
			
		}
	
	}
	
	public function parseHtmlForXhr($body) {
	
		// need to remove comments 
		$body = preg_replace(array("/\/\/[a-zA-Z0-9\s\&\?\.]+\n/","/\/\*(.*)\*\//")," ",$body);
		
		// javascript 		
		$jsInPage = preg_match_all("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i",$body,$js);		
		
	
			// if yes remove 
			if ( $jsInPage ) {
				$body = preg_replace("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i","",$body);
			}	
		
		// give back
		return array($body,@$js[3]);
	
	}

	/**
	 * get a template path
	 * @method	tmpl
	 */
	public function tmpl($file) {
	
		// check for ending
		if ( strpos($file,'.tmpl.php') === false ) {
			$file .= '.tmpl.php';
		}
	
		// return
		return $this->config['dir']['tmpl'] . $file;
	
	}
	
	public function setCache($cid,$data,$ttl) {		
		if ( $this->mem ) {
			return $this->mem->set($cid,$data,MEMCACHE_COMPRESSED,$ttl);
		}
		return false;
	}

	public function getCache($cid) {
		if ( $this->mem ) {
			return $this->mem->get($cid);
		}
		return false;
	}			
	
	public function go($url) {
		exit( header("Location:".$url) );
	}
	
	public function validateStr($str,$as,$return=false) {
	
		// what up
		if ( $as == 'hosturl' ) {
			
			// parse
			$host = parse_url($str,PHP_URL_HOST);
			$local = parse_url(HOST,PHP_URL_HOST);
			
			if ( $host == $local ) {
				return $str;
			}
			
		}
	
		// bad
		return $return;
	
	}
	
			
			/**
	Validate an email address.
	Provide email address (raw input)
	Returns true if the email address has the email 
	address format and the domain exists.
	*/
	function validateEmail($email)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	   {
	      $isValid = false;
	   }
	   else
	   {
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64)
	      {
	         // local part length exceeded
	         $isValid = false;
	      }
	      else if ($domainLen < 1 || $domainLen > 255)
	      {
	         // domain part length exceeded
	         $isValid = false;
	      }
	      else if ($local[0] == '.' || $local[$localLen-1] == '.')
	      {
	         // local part starts or ends with '.'
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $local))
	      {
	         // local part has two consecutive dots
	         $isValid = false;
	      }
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	      {
	         // character not valid in domain part
	         $isValid = false;
	      }
	      else if (preg_match('/\\.\\./', $domain))
	      {
	         // domain part has two consecutive dots
	         $isValid = false;
	      }
	      else if
	(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
	                 str_replace("\\\\","",$local)))
	      {
	         // character not valid in local part unless 
	         // local part is quoted
	         if (!preg_match('/^"(\\\\"|[^"])+"$/',
	             str_replace("\\\\","",$local)))
	         {
	            $isValid = false;
	         }
	      }
	   }
	   return $isValid;
	}
	
    public function randomString($len=30) {
        // chars
        $chars = array(
                'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','V','T','V','U','V','W','X','Y','Z',
                '1','2','3','4','5','6','7','8','9','0'
        );
       
        // suffle
        shuffle($chars);
       
        // string
        $str = '';
       
        // do it
        for ( $i = 0; $i < $len; $i++ ) {
                $str .= $chars[array_rand($chars)];
        }
       
        return $str;   

	}		
	
	
} // END framework






?>