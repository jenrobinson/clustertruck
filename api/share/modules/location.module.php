<?php

// truck
class location extends Api {
    
    // methods
    public static $methods = array('get');
	
	// get 
    public function get() {
    
    	// id
    	$id = pp(0);
		$start = p('start',time());
		$stop = p('stop');    	
    	
    	// check for , which means lat long
		if ( strpos($id,',') ) {
			$id = md5( str_replace(',','',$id) );
		}
		
			// no id
			if ( !$id ) {
				self::errorDoc("No Location Id or Lat/Long provided",400);
			}
		
		// where
		$where = array('MD5(CONCAT(l.lat,l.lon)) = ?');
		$p = array($id);
    
    		// start
    		if ( $start ) {
    			$where[] = " l.time_start >= ? ";
    			$p[] = $start;
    		}  
    		
    		// stop
    		if ( $stop ) {
    			$where[] = " l.time_stop <= ? ";
    			$p[] = $stop;
    		}    
    
    	// slq
    	$sql = "
    		SELECT 
    			l.*,
    			t.id,
    			t.slug,
    			t.name,
    			t.description
    		FROM 
    			trucks as t,
    			locations as l 
    		WHERE 
				".implode(' AND ',$where)." AND
    			t.id = l.truck_id
    	";    	
    	
    	// sql
    	$sth = $this->query($sql,$p);
    	
    	// lets do it 
    	$resp = array(
    		'@' => array( 'id' => $id ),
    	);
    
    	// need at least one
		if ( count($sth) > 0 ) {
		
			// loc
			$loc = $sth[0];
			
			// add
			$resp['address'] = $loc['address'];
			$resp['lat'] = $loc['lat'];
			$resp['long'] = $loc['lon'];
			
			// resp
			$resp['trucks'] = array( '@' => array('count' => count($sth) ) );
			
    		// each truck 
    		foreach ( $sth as $row ) { 
    			$resp['trucks'][] = array(
    				'_item' => 'truck',
	 				'@' => array(
		 				'id' => $row['id'],
		 				'slug' => $row['slug']
		 			),
		 			'name' => $row['name'],
					'*description' => $row['description'],
					'start' => array( '@' => array( 'timestamp' => $row['time_start'] ), '_value' => date('c',$row['time_start']) ),
					'end' =>  array( '@' => array( 'timestamp' => $row['time_stop'] ), '_value' => date('c',$row['time_stop']) ),					
    			);
    		}   			
			
		}
    
    	// return
    	return array( 'location' => $resp );
    
    }

}

?>