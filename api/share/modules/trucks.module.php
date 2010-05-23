<?php

// truck
class trucks extends Api {
    
    // methods
    public static $methods = array('get');
	
	// get 
    public function get() {

		// page and per
		$page = (int)p('page',1);
		$per = (int)p('per',20);
			
			// not mroe than 20
			if ( $per > 20 ) { $per = 20; }

		// start
		$start = ($page-1)*$per;
		
		// where
		$where = array('1');
		$p = array( time());

			// query
			if ( p('name') ) {
			
				// get the name
				$name = str_replace("*","%",p('name'));
				
				// query it
				$where[] = " LOWER(t.name) LIKE LOWER(?) ";
				
				// param
				$p[] = $name;
				
			}
			
		// sql
		$sql = "
			SELECT 			
				t.id,
				t.slug,
				t.name,
				t.description,
				l.name as loc_name,
				l.address,
				l.lat,
				l.lon,
				l.time_start,
				l.time_stop,
				l.id as loc_id
			FROM 
				trucks as t
			LEFT JOIN
				locations as l ON ( t.id = l.truck_id AND l.time_start > ? )
			WHERE 
				".implode(" AND ",$where)."
			GROUP BY (t.id)
			ORDER BY t.name,l.time_start
			LIMIT {$start},{$per}
		";
		
		// $total
		$total = true;

		// get a list of trucks
		$sth = $this->query($sql,$p,$total);
		
		// how many pages
		$pages = ( $total > 1 ? ceil($total/$per) : 0 );
		 
		// resp
		$resp = array( '@' => array( 'total' => $total, 'pages' => $pages ) );
 
 		// return them
 		foreach ( $sth as $row ) {
 			
 			// r
 			$r = array(
 				'_item' => 'truck',
 				'@' => array(
	 				'id' => $row['id'],
	 				'slug' => $row['slug']
	 			),
	 			'name' => $row['name'],
				'*description' => $row['description'],
				'location' => ""
 			);
 			
 			// check loc
 			if ( !empty($row['loc_id']) ) {
 				$r['location'] = array(
 					'@' => array('id' => $row['loc_id']),
 					'name' => $row['address'],
 					'lat' => $row['lat'],
 					'long' => $row['lon'],
					'start' => array( '@' => array( 'timestamp' => $row['time_start'] ), '_value' => date('c',$row['time_start']) ),
					'end' =>  array( '@' => array( 'timestamp' => $row['time_stop'] ), '_value' => date('c',$row['time_stop']) ),
 				);
 			}
 			
 			// add to resp
 			$resp[] = $r;
 			
 		}
 
 		// give back
 		return array('trucks' => $resp);
 
	}
	
}

?>