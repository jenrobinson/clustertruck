<?php

	$this->header = $area['name'];

?>

<ul class="touch">
<?php
	
		if (empty($points)) { 
			
			echo '<p class="text">No '.$area['name'].' Appearances Scheduled</p>';
		
		}
		
	
		foreach ($points as $s) { 
			
			$truck = json_decode($s['truck_info'],true);
						
				echo'
					<li class="location">
						<a href="'.URI.'m/trucks/'.$truck['slug'].'">
							<img class="icon" idth="48" height="48" src="'.$truck['twitter_image'].'" />
							<em>'.$truck['name'].' at '.$s['name'].'</em>
							<p>'.$s['datetime'].'</p>	
							<span class="arrow">></span>
						</a>
					</li>';				
		
		}
	?>
</ul>