<?php

/**
* Map Module Class
*
* This class handles logic related to map pages including the homepage and neighborhood.
*
*/

class map extends Fe {

	public function __construct() {
	
		parent::__construct();
	
	}
	
	// get model and view for the homepage
	public function live() {
		
		$points = $this->getSchedule(false,p('filter',false));
		$lastUpdated = $this->getLastUpdateTime();
	
		// include main
		include( $this->tmpl('map/live') );
	
	}
	
	// get model and view for the list of neighborhoods
	public function arealist() {
	
		$areas = $this->getAreas();
	
		// include main
		include( $this->tmpl('map/area-list.tmpl.php') );
	
	}
	
	// get model and view for the map page for an area
	public function area() {
	
		$area = $this->getArea(p('slug',false));
		
		$schedule = $this->getSchedule(false,p('filter',false));
		
		$points = array();
		
		$i = 0;
		
		foreach ($schedule as $s) { 

			// check if the scheduled point is within that neighborhood's radius			
			if ($this->distance($area['lat'],$area['lon'],$s['lat'],$s['lon'],'M') < $area['miles_large']) { 
			
				$points[$i] = $s;			
				$i++;
				
			}
	
		}
	
		$mapConfig = array();
		$mapConfig['zoom'] = 13; 
		$mapConfig['lat'] = $area['lat']; 
		$mapConfig['lon'] = $area['lon']; 
	
		// include area template
		include( $this->tmpl('map/area') );
	
	}
	
	
	// distance function calculates if a LAT and LON are without the radius of a center point
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


?>