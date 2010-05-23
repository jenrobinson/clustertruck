<?php

/**
* Mobile Module Class
*
* This class handles page mapping logic for the mobile site, 
* which uses separate templates to give a different
* view of the same data.
*
*/

class mobile extends Fe {

	public function __construct() {
	
		parent::__construct();
		
		$this->header = 'Cluster Truck';
	
	}
	
	public function index() {
		
		$trucks = $this->getTrucks();
		
		if ($this->loged) { 	
		
			$truck = $this->getTruckByTwitter($this->user['name']);
			
		} else { 
			
			$truck = false;
		
		}
				
		// include main
		include( $this->tmpl('mobile/index') );
	
	}
	
	
	public function map() {
		
		$schedule = $this->getSchedule(false,p('filter',false));
						
		// include main
		include( $this->tmpl('mobile/map') );
	
	}
	
	
	public function location() {
		
		$schedule = $this->getSchedule(false,p('id',false));
		$truck = json_decode($schedule[0]['truck_info'],true);						
		
		// include main
		
		include( $this->tmpl('mobile/location') );
	
	}
	
	
	public function truck() {
		
		$slug = p('slug');
		$this->truck = $this->getTruck($slug); 
		
		//watch for deletion of a location
		if ($location = p('deleteLocation')) { 
			
			if ($this->hasEditAccess()) { 
				$r = $this->query("DELETE FROM locations WHERE id = ? LIMIT 1",array($location));
			}
			
			header('Location: '.URI.'m/trucks/'.$slug);
		}
		
		//check for new schedule post
		if (p('form-class') == 'location' && $this->hasEditAccess()) { 
		
			$truck_id = p('truck_id');
			$truck_twitter = $this->truck['twitter'];
			$location_id = p('location_id',false);
			$name = p('location_name');
			$address = p('address');
			$date = p('date');
			$time_start = (int)strtotime($date . ' ' .p('time_start'));
			$time_end = (int)strtotime($date . ' ' .p('time_end'));
			
			//adjust time end if it's after midnight
			if ($time_end < $time_start) { 
				
				$time_end = $time_end + 86400;
			
			}
			
			$notes = p('notes');
			
			//get the lat / lon from address
			if (!empty($address)) { 
				
				$url = 'http://local.yahooapis.com/MapsService/V1/geocode?appid='.YAHOO_APP_ID.'&location='.urlencode($address);
				
				//make a call to Yahoo to get the lat/lon from the address provided
				$response = $this->doCurl($url);
				
				$geo = simplexml_load_string($response);
				
				$lat = $geo->Result->Latitude;
				$lon = $geo->Result->Longitude;
						
				
				if ($location_id) { 
				
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
							$notes,
							$location_id						
						));
				
				} else { 
				
					// do me up
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
							$notes						
						));
					
					if ($r) { 
						
						//add message telling them that they inserted
						echo '<h2 class="highlight">Added Location!</h2>';
					
					}
				
				}
			
			
			} else { 
				
				$formError = true;
			
			}
			
					
		}
		
		
		
		
		
		$schedule = $this->getSchedule($this->truck['id'],false);
						
		// include main
		include( $this->tmpl('mobile/truck') );
	
	}
	
	public function trucks() {
		
		$trucks = $this->getTrucks();
						
		// include main
		include( $this->tmpl('mobile/trucks') );
	
	}
	
	public function neighborhood() { 
	
		$area = $this->getArea(p('slug',false)); 
		
		$schedule = $this->getSchedule(false,p('filter',false));
		
		$points = array();
		
		$i = 0;
		
		foreach ($schedule as $s) { 
		
			if ($this->distance($area['lat'],$area['lon'],$s['lat'],$s['lon'],'M') < $area['miles_large']) { 
			
				$points[$i] = $s;			
				$i++;
				
			}
	
		}
				
		// include main
		include( $this->tmpl('mobile/neighborhood') );
	
	}
	
	public function neighborhoods() {
	
		$neighborhoods = $this->getAreas();
						
		// include main
		include( $this->tmpl('mobile/neighborhoods') );
	
	}
	
	
	public function distance($lat1, $lon1, $lat2, $lon2, $unit='M') { 

		  $theta = $lon1 - $lon2; 
		  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
		  $dist = acos($dist); 
		  $dist = rad2deg($dist); 
		  $miles = $dist * 60 * 1.1515;
		  $unit = strtoupper($unit);
		
		  if ($unit == "K") {
		    	return ($miles * 1.609344); 
		  	} else if ($unit == "N") {
	      		return ($miles * 0.8684);
	    	} else {
	        	return $miles;
	      }
	}
	
	
	
}

