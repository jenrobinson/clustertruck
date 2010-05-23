<?php

	//set meta title
	$this->metaTitle = $area['name'] .' Food Truck Map on Cluster Truck ';
	$this->metaDescription = 'Find food trucks scheduled to visit '.$area['name'];
	
?>

<h1><?php echo $area['name']; ?> Food Trucks</h1>
<div class="left">
	<div id="map">Loading Map...</div>
</div>

<div class="locations">
	<h2>Scheduled Trucks</h2>
	<ul>

	<?php
	
		$count = 0;
	
		foreach ($points as $p) { 
			
			$truck = json_decode($p['truck_info'],true);
			
			if ($count == 0) { $class = 'selected'; } else if ($count < 5) { $class = ''; } else { $class = "icon"; }
			
			if ($count < 5) { 
			
				echo'
					<li id="s'.$p['id'].'" class="'.$class.' location">
						<img width="48" height="48" src="'.$truck['twitter_image'].'" />
						<em>'.$truck['name'].' at '.$p['name'].'</em>
						<p>'.$p['datetime'].'<br/> <a href="'.URI.$truck['slug'].'">Profile &#187;</a></p>
						
					</li>';
					
			} else { 
			
				echo'
					<li id="s'.$p['id'].'" class="'.$class.' location">
						<img width="48" height="48" src="'.$truck['twitter_image'].'" />
					</li>';
					
			}
						
							
			$count++;
		
		}
	?>
	
	</ul>
	
</div>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">

	var mapConfig = <?php echo json_encode($mapConfig); ?>;
	var points = <?php echo json_encode($points); ?>;

</script>