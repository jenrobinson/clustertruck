<ul class="touch">
<?php
		
		$this->header = 'Live Schedule';
			
		foreach ($schedule as $s) { 
			
			$truck = json_decode($s['truck_info'],true);
			
			$mapsLink = URI.'m/map/'.$s['id'];
						
				echo'
					<li class="location">
						<a href="'.$mapsLink.'">
							<img class="icon" idth="48" height="48" src="'.$truck['twitter_image'].'" />
							<em>'.$truck['name'].' at '.$s['name'].'</em>
							<p>'.$s['datetime'].'</p>	
							<span class="google">View Map &#187;</span>
						</a>
					</li>';				
		
		}
	?>
</ul>