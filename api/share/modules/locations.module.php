<?php

// truck
class locations extends Api {
    
    // methods
    public static $methods = array('get');
	
	// get 
    public function get() {
    
    	// lat and long
    	$lat = p('lat');
    	$long = p('long');
    	$distance = p('distance');
		$start = p('start',time());
		$stop = p('stop');
		$page = (int)p('page',1);
		$per = (int)p('per',20);
		
			// no more than 20
			if ( $per > 20 ) {
				$per = 20;
			}
		
		// if distance we need lat long 
		if ( $distance AND ( !$lat OR !$long ) ) {
			self::errorDoc("If requesting a distance, you must specify a `lat` and `long`",400);
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
    	
    		// lat and long
    		if ( $lat ) {
    			$where[] = " l.lat = ? ";
    			$p[] = $lat;
    		}
    		
    		if ( $long ) {
    			$where[] = " l.lon = ? ";
    			$p[] = $long;
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
	    	if ( $distance ) {
	    		foreach ( $sth as $i => $row ) {
	    			if ( $this->distance($row['lat'],$row['lon'],$lat,$long) > $distance ) {
	    				unset($sth[$i]);
	    			}
	    		}	    	
	    	}
	    	
	    	// locs
	    	$locs = array();
	    	
	    	// group locations together
			foreach ( $sth as $row ) {
				$id = md5($row['lat'].$row['lon']);
				$locs[$id][] = $row;
			}
	    	
	    // total
	    $total = count($locs);
	    $pages = ( $total > 0 ? ceil($total/$per) : 0 );
	    $start = ($pages-1) * $per;
	    	
	    // resp
	    $resp = array( '@' => array('total'=>$total,'pages'=>$pages) );
    
    	// add them
    	foreach ( array_slice($locs,$start,$per) as $id => $trucks ) {
    		
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
    		
    		// add to resp
    		$resp[] = $r;
    		
    	}
    
    	// give bak
    	return array( 'locations' => $resp );
    
    }

    
}

?>