<?php

// truck
class truck extends Api {
    
    // methods
    public static $methods = array('get');
	
	// get 
    public function get() {

		// check for id
		$id = pp(0);
		$show = explode(',',pp(1,'info,menu,locations,twitter'));

			// no truck
			if ( !$id OR !$show ) {
				self::errorDoc("No Truck Id or View information provided.",400);
			}
	
		// get a truck
		$truck = $this->row("SELECT * FROM trucks as t WHERE t.slug = ? OR t.id = ? ",array($id,$id));
	
			// no truck
			if ( !$truck ) {
				self::errorDoc("Could not find truck '{$id}'.",404);
			}
		
		// explode
		$resp = array('@' => array('id'=>$truck['id'],'slug'=>$truck['slug']));
		
		// info
		if ( in_array('info',$show) ) {
			
			$resp['info'] = array(
				'name'	=> $truck['name'],
				'*description' => $truck['description'],
				'website' => $truck['website'],
				'images' => array(
					'feature' => array(
						'url' => $truck['feature_image'],
						'attribution' => $truck['feature_image_attr'],					
					),
					'twitter' => array(
						'url' => $truck['twitter_image'],						
					)
				),
			);
		
		}
		
		// menu
		if ( in_array('menu',$show) ) {
		
			// get it 
			$menu = json_decode($truck['menu'],true);
		
			// add it 
			$resp['menu'] = array( '@' => array('count'=>count($menu)) );
			
			// menu
			if ( $menu ) {
					
				// items
				foreach ( $menu as $item ) {
					$resp['menu'][] = array(
						'_item' => 'item',
						'@' => array('id'=>$item['id']),
						'name' => $item['item'],
						'*description' => $item['description'],
						'image' => array(
							'url' => $item['image']
						),
						'price' => number_format((float)$item['price'],2)
					);
				}
				
			}
		
		}
		
		// locations
		if ( in_array('locations',$show) ) {
			
			// get the locations
			$sth = $this->query("SELECT * FROM `locations` as l WHERE l.truck_id = ? AND l.time_start > ? ORDER BY l.time_start LIMIT 50 ",array($truck['id'],time()) );
		
			// add it 
			$resp['locations'] = array( '@' => array('count'=>count($sth)));
		
			// go for it
			foreach ( $sth as $row ) {
				$resp['locations'][] = array(
					'_item' => 'location',
					'name' => $row['name'],
					'location' => array(
						'address' => $row['address'],
						'lat' => $row['lat'],
						'long' => $row['lon']
					),
					'start' => array( '@' => array( 'timestamp' => $row['time_start'] ), '_value' => date('c',$row['time_start']) ),
					'end' =>  array( '@' => array( 'timestamp' => $row['time_stop'] ), '_value' => date('c',$row['time_stop']) ),
					'*notes' => $row['notes']
				);
			}
		
		}
		
		// give back respone
		return array('truck' => $resp );
		
	}
	
}

?>