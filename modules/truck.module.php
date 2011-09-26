<?php

/**
* Truck Module Class
*
* This class handles logic for truck pages, mainly the truck profile page.
*
*/

class truck extends Fe {

	public function __construct() {
	
		parent::__construct();
	
	}
	
	// handles all logic, including saving for the truck profile page
	public function profile() {
	
		$formError = false;
		
		$slug = p('slug');
		$this->truck = $profile = $this->getTruck($slug); 
		
		//watch for deletion of a location
		if ($location = p('deleteLocation')) { 
			
			$s = $this->row("SELECT * FROM locations WHERE id = ? LIMIT 1",array($location));
						
			if ($this->hasEditAccess() || ($this->loged && $this->user['name'] == $s['added_by']) ) { 
				$r = $this->query("DELETE FROM locations WHERE id = ? LIMIT 1",array($location));
			}
			
			header('Location: '.URI.$slug);
		}
				
		//check for new schedule post
		if (p('form-class') == 'location' && $this->loged) { 
			
			$truck_id = p('truck_id');
			$truck_twitter = $this->truck['twitter'];
			$location_id = p('location_id',false);
			$name = p('location_name');
			$address = p('address');
			$date = p('date');
			$time_start = (int)strtotime($date . ' ' .p('time_start'));
			$time_end = (int)strtotime($date . ' ' .p('time_end'));
			
			$tweet_to_truck = p('tweet_to_truck',false);
			$tweet_to_public = p('tweet_to_public',false);
			
			$user = $this->user['name'];
			
			//adjust time end date if it's after midnight
			if ($time_end < $time_start) { 
				
				$time_end = $time_end + 86400;
			
			}
			
			$tstrt = p('time_start');
			$tstp = p('time_end');
			
			$notes = p('notes');
				
			//catch all the errors
			if (empty($name)) { 
				$formError = true;
				$errorMsg = 'You are missing a name for your location.';
			}
			else if (empty($address)) { 
				$formError = true;
				$errorMsg = 'Please specify an address in the format: Street (or Intersection), City, State.';
			} 	
			else if (empty($truck_twitter)) { 
				$formError = true;
				$errorMsg = 'That truck does not have a Twitter account associated with it.';
			}			
			else if (empty($date)) { 
				$formError = true;
				$errorMsg = 'You must specify a date in the MM/DD/YY format.';
			}
			else if (empty($tstrt)) { 
				$formError = true;
				$errorMsg = 'You must specify a starting time.';
			}
			else if (empty($tstp)) { 
				$formError = true;
				$errorMsg = 'You must specify a ending time.';
			}
			
			
			
			
			if (!$formError) { 
			
				//ok do the call to google for geocoding the address entered								
				$url = 'http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false';
				$response = $this->doCurl($url);
				
				$geo = json_decode($response);
				
				// if the status is OK then let's use the data				
				if ($geo->status == 'OK') { 
					
					// get lat and lon
					$lat = $geo->results[0]->geometry->location->lat;
					$lon = $geo->results[0]->geometry->location->lng;
					
					//problem - set the formError				
					if (empty($lat) || empty($lon)) { 
						$formError = true;
						$errorMsg = 'Location entered was not valid. Make sure you include the City, State.';
					}				
					
					if ($location_id && !$formError) { 
						
						// get the location to edit
						$s = $this->row("SELECT * FROM locations WHERE id = ? LIMIT 1",array($location_id));
						
						//make sure they have access to edit this scheduled location	
						if ($this->hasEditAccess() || ($this->loged && $this->user['name'] == $s['added_by']) ) { 
					
								// do me up
								$sql = "
									UPDATE
										`locations`
									SET 
										`truck_id` = ?,
										`truck_info` = ?,
										`name` = ?,
										`address` = ?,
										`lat` = ?,
										`lon` = ?,
										`time_start` = ?,
										`time_stop` = ?,
										`added_when` = ?,
										`notes` = ?
									WHERE `id` = ?
									LIMIT 1
								";
											
								// run it
								$r = $this->query($sql,array(
										$truck_id,
										json_encode($this->truck),
										$name,
										$address,
										$lat,
										$lon,
										$time_start,
										$time_end,
										strtotime('now'),
										$notes,
										$location_id						
									));
							
						}
					
					} else if (!$formError) { 
					
						// we can insert a new schedule location to the db
						$sql = "
							INSERT INTO
								`locations`
							SET 
								`truck_id` = ?,
								`truck_info` = ?,
								`name` = ?,
								`address` = ?,
								`lat` = ?,
								`lon` = ?,
								`time_start` = ?,
								`time_stop` = ?,
								`added_by` = ?,
								`added_when` = ?,
								`notes` = ?
						";
									
						// run it
						$r = $this->query($sql,array(
								$truck_id,
								json_encode($this->truck),
								$name,
								$address,
								$lat,
								$lon,
								$time_start,
								$time_end,
								$user,
								strtotime('now'),
								$notes						
							));
							
						// add to tweet queue if user requested
						if ($tweet_to_truck == 'on') { 
								
								// construct a tweet
								$truck_tweet = 'added "'.$name.'" '.date("D M j, g:ia",$time_start). ' to the map';
								
								//add tweet to queue
								$sql = "
									INSERT INTO
										`tweet_queue`
									SET 
										`tweet` = ?,
										`to` = ?,
										`link_url` = ?,
										`timestp` = ?,
										`expire` = ?
									";
																		
								// run it
								$r = $this->query($sql,array(
										$truck_tweet,
										$truck_twitter,
										URI.$slug,
										strtotime('now'),
										$time_end
									));
							
						}
						
						
						// add to tweet queue if user requested
						if ($tweet_to_public == 'on') { 
								
								// construct a tweet
								$truck_tweet = 'Added @'.$truck_twitter.' at "'.$name.'" on '.date("D M j, g:ia",$time_start). ' to the map';
								
								//add tweet to queue
								$sql = "
									INSERT INTO
										`tweet_queue`
									SET 
										`tweet` = ?,
										`link_url` = ?,
										`timestp` = ?,
										`expire` = ?
									";
																		
								// run it
								$r = $this->query($sql,array(
										$truck_tweet,
										URI.$slug,
										strtotime('now'),
										$time_end
									));
							
						}
						
						
						
						
						if ($r) { 
							
							//add message telling them that they inserted
						
						}
					
					}
			
				} // end status check for google geo
				
			} 
		
		
		}
		
		// if we are returning any result through AJAX then let's just give back json
		if ($this->context == 'xhr') {
			
			if ($formError) { 
				
				echo '<script>'.$this->printJsonResponse(array('do'=>'error','msg'=>$errorMsg)).'</script>';
				
			} else { 
				
				echo '<script>'.$this->printJsonResponse(array('do'=>'redi','url'=>URI.$slug)).'</script>';
				
			}
			
			return;
		
		} 	
		
		
		
		
		
		//process images if uploaded
		if (p('form-class') == 'profile' && $this->hasEditAccess()) {
			
			if (!empty($_FILES['uimage'])) { 
				$image = (string)$_FILES['uimage']['name']; 
			}
			
			if (!empty($image)) { 
				
				// process the image and send it to Amazon S3
				$image = $this->processUploadedImage();
			
			} else { 
				
				// keep the old image if a new one is not posted
				$image = p('oldimage');
			
			}
			
			//update the image for this truck
				$sql = "
					UPDATE
						`trucks`
					SET 
						`name` = ?,
						`description` = ?,
						`twitter` = ?,
						`website` = ?,
						`feature_image` = ?,
						`feature_image_attr` = ?
					
					WHERE `slug` = ?
					LIMIT 1
				";
							
				// run it
				$result = $this->query($sql,array(
						p('name'),
						p('description'),
						p('twitter'),
						p('website'),
						$image,
						p('feature_image_attr'),
						$slug						
				));
			
			if ($this->context == 'xhr') {
				
				echo '<script>'.$this->printJsonResponse(array('do'=>'redi','url'=>URI.$slug)).'</script>';
				
				return;
			}
		
		}
		
		
		//add menu item if we want
		if (p('form-class') == 'menu') {
			
			if (isset($_FILES['uimage'])) { 
				$image = $_FILES['uimage'];
			}
			
			if (!empty($image)) { 
				
				$image = $this->processUploadedImage();
			
			} else { 
				
				$image = p('oldimage','');
			
			}
						
			$menu = json_decode($this->truck['menu'],true);
			
			$desc = p('description','');
			
			$itemId = p('item_id',uniqid());
			
			$key = false;
			
			if (!empty($menu)) { 
			
				foreach ($menu as $k=>$v) { 
					if ($v['id'] == $itemId) { 
						$key = $k;
						break;
					}
				}
			
			}	
					
			if (is_numeric($key)) {} 
			else { 
				
				//find a suitable key for this menu item in the array
				for($x=count($menu); $x < count($menu) + 100; $x++) { 
					if (empty($menu[$x])) { 
						$key = $x;
						break;
					}
				} 
				
			}
			
			// construct a menu item array for our data						
			$menu[$key] = array(
						'id'=>p('item_id',uniqid()),
						'image'=>$image,
						'item'=>p('item','No Name'),
						'description'=>p('description',''),
						'price'=>p('price',''),
						'added_by'=>$this->user['name']
					);
			
			if (is_numeric($key) && !empty($menu)) { 
			//update the image for this menu
				$sql = "
					UPDATE
						`trucks`
					SET 
						`menu` = ?
					
					WHERE `id` = ?
					LIMIT 1
				";
							
				// run it
				$result = $this->query($sql,array(
						json_encode($menu),
						p('truck_id')						
				));
				
			}
				
			if ($this->context == 'xhr') { 
				
				echo '<script>'.$this->printJsonResponse(array('do'=>'redi','url'=>URI.$slug)).'</script>';
				
				return;
			
			}
		
		}
		
		// delete the menu item from our data
		if ($item = p('deleteMenuItem')) { 
		
			$menu = json_decode($this->truck['menu'],true);
			
			foreach ($menu as $k=>$v) { 
			
				if ($v['id'] == $item && ($v['added_by'] == $this->user['name'] || $this->hasEditAccess()) ) {  
					
					unset($menu[$k]);
					
					//update the menu in the db
					$sql = "
						UPDATE
							`trucks`
						SET 
							`menu` = ?
						
						WHERE `slug` = ?
						LIMIT 1
					";
								
					// run it
					$result = $this->query($sql,array(
							json_encode($menu),
							p('slug')						
					));
					
					// send to clean page (no post)
					header('Location: '.URI.$slug);
					
					break;
					
				}
				
			}
			
		}
		
		
		// get a profile for the slug coming from the browser, if not real go to 404
		if ($profile = $this->getTruck($slug)) { 
			
			$schedule = $this->getSchedule($profile['id'],p('filter',false));
			
			// include main
			include( $this->tmpl('truck/profile') );
			
		} else { 
			
			// include main
			include( $this->tmpl('404') );
		
		}
	
	}
	
	public function trucks() { 
		
		
		//if you're in the god role and you can add trucks, let's do it
		if (p('form-class') == 'add-truck' && $this->hasEditAccess()) {
			
			if (!empty($_FILES['uimage'])) { 
				$image = (string)$_FILES['uimage']['name']; 
			}
			
			if (!empty($image)) { 
			
				$image = $this->processUploadedImage();
			
			} else { 
				
				$image = p('oldimage','');
			
			}
			
			// get twitter image
			if (p('twitter',false)) { 
			
				// make a call to twitter API to get your pic
				                                
                                $response       = $this->twitter->http('http://api.twitter.com/1/users/show.json?screen_name='.p('twitter').'&include_entities=true', 'GET');
                                $user_data      = json_decode($response);                                
                                $twitter_image  = $user_data->profile_image_url;
				
			} else { 
			
				$twitter = '';
				$twitter_image = '';
			
			}
			
			
			//insert this new truck into the DB
				$sql = "
					INSERT INTO
						`trucks`
					SET 
						`slug` = ?,
						`name` = ?,
						`description` = ?,
						`twitter` = ?,
						`twitter_image` = ?,
						`website` = ?,
						`feature_image` = ?,
						`feature_image_attr` = ?
					
				";
							
				// run it
				$result = $this->query($sql,array(
						p('slug'),
						p('name'),
						p('description'),
						p('twitter'),
						$twitter_image,
						p('website'),
						$image,
						p('feature_image_attr'),						
				));
			
			if ($this->context == 'xhr') {
				
				echo '<script>'.$this->printJsonResponse(array('do'=>'redi','url'=>URI.$slug)).'</script>';
				
				return;
			}
		
		}
		
		
		// get the new truck list and return it for rendering
		$trucks = $this->getTrucks();
		
		include( $this->tmpl('truck/list') );
	
	}
	
	
	public function add() { 
		
		// add a new truck template		
		include( $this->tmpl('truck/forms/add') );
	
	}
	
	
	public function validateslug() { 
		
		// we need to make sure a slug is not duplicate, that would cause issues	
		$slug = p('slug');
		
		$r = $this->query("SELECT slug FROM trucks WHERE slug = ? LIMIT 1",array($slug));
		
		if (empty($r)) { 
			$good = "good";
		} else { 
			$good = "bad";
		}
		
		if ($this->context == 'xhr') { 
				
				echo '<script>'.$this->printJsonResponse(array('validslug'=>$good)).'</script>';
				
				return;
			
		}

	
	}
	
	
	public function menu() { 
		
		// load the template for editing / add to the menu for a truck
		$slug = p('slug');
		$this->truck = $profile = $this->getTruck($slug);
		
		if ($itemId = p('item',false)) { 
			
			//get the item from menu
			$menuItems = json_decode($this->truck['menu'],true);
			$count = 0;
		
			foreach ($menuItems as $k=>$v) {
						
				if ($v['id'] == $itemId) { 
					
					$item = $menuItems[$k];
					break;
				
				}
				
			}
			
		} 
		
		include( $this->tmpl('truck/forms/menu') );
	
	}
	
	// load the template for adding / editing a particular scheduled location
	public function location() { 
		
		$return = $this->getReturnToken();
		
		// if not logged in, take them to twitter for auth
		if (!$this->loged) { 
			header('Location: '.$this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))));
		}
		
		$slug = p('slug');
		$locationId = p('location',false);
		
		if ($locationId) { 
			$location = $this->getScheduledLocation($locationId);
		} else { 
			$location = false;
		}
		
		$this->truck = $profile = $this->getTruck($slug); 
		
		include( $this->tmpl('truck/forms/location') );
	
	}
	
	// load the panel template for editing a particular truck profile
	public function editdetails() { 
		
		$slug = p('slug');
		$this->truck = $profile = $this->getTruck($slug); 
		
		include( $this->tmpl('truck/forms/edit-details') );
	
	}
	
	// some secret special logic here to let me easily enter / parse photo credits on Flickr
	// @c if you format it like this it will parse and make html for you
	// flickr:userid:photoid (ex flickr:rochers:52342344)
	// link:url:labeltext (ex link:buttermilktruck.com:Buttermilk Truck)
	
	public function parsePhotoCredit($c) { 
	
		$attr = explode(":",$c);
		
		//is flickr?
		if ($attr[0] == 'flickr') { 
			
			$c = '<a target="_blank"  href="http://www.flickr.com/photos/'.$attr['1'].'/'.$attr['2'].'/">'.$attr['1'].'</a>';
		
		} else //is link?
		if ($attr[0] == 'link') { 
		
			$c = '<a target="_blank"  href="http://'.$attr['2'].'">'.$attr['1'].'</a>';
			
		}
		
		return $c;
	
	}
	


}


?>