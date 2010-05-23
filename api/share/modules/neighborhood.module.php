<?php

// truck
class neighborhood extends Api {
    
    // methods
    public static $methods = array('get');
	
	// get 
    public function get() {
    
    	// id
    	$id = pp(0);
		$start = p('start',time());
		$stop = p('stop');    	
    		
    		// required
    		if ( !$id ) {
    			self::errorDoc("No Neightborhood Id or Slug provided.",400);
    		}
    
    	// get it 
    	$hood = $this->row(" SELECT * FROM `areas` as a WHERE a.id = ? OR a.slug = ? ",array($id,$id));
    
    		// no hood
    		if ( !$hood ) {
    			self::errorDoc("Could not find neighborhood '$id'",404);
    		}
    
		// where & p
		$where = array(1);
		$p = array();		
    	
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
    
    	// sth
    	$sth = $this->query($sql,$p);
    
    	// if distance we need to limit
		foreach ( $sth as $i => $row ) {
			if ( $this->distance($row['lat'],$row['lon'],$hood['lat'],$hood['lon']) > $hood['miles_large'] ) {
				unset($sth[$i]);
			}
		}	    	
	    	
	    	// locs
	    	$locs = array();
	    	
	    	// group locations together
			foreach ( $sth as $row ) {
				$id = md5($row['lat'].$row['lon']);
				$locs[$id][] = $row;
			}    	
    
    	// resp
    	$resp = array( 
    		'@' => array(
    			'id'=>$hood['id'],
    			'slug'=>$hood['slug']
    		),
    		'name' => $hood['name'],
    		'center' => array(
    			'lat' => $hood['lat'],
    			'long' => $hood['lon']
    		),
    		'locations' => array( 
    			'@' => array( 'count' => count($locs) ) 
    		)
    	);
    
    	// add them
		foreach ( $locs as $id => $trucks ) {
		
    		// row
    		$row = $trucks[0];
    		
    		// r
    		$r = array(
    			'_item' => 'location',
    			'@' => array( 'id' => $id ),
    			'address' => $row['address'],
    			'lat' => $row['lat'],
    			'long' => $row['lon'],
    			'trucks' => array( '@' => array( 'count' => count($trucks) ) )
    		);
    			
    			
    		// each truck 
    		foreach ( $trucks as $row ) { 
    			$r['trucks'][] = array(
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
		
			$resp['locations'][] = $r;
		
		}
    
    	// give it
    	return array('neighborhood' => $resp);
    
    }

}
    
?>