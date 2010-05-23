<ul class="touch">
<?php

		$this->header = 'Trucks';
	
		foreach ($trucks as $t) { 
						
				echo'
					<li class="truck">
						<a href="'.URI.'m/trucks/'.$t['slug'].'">
							<img class="icon" idth="48" height="48" src="'.$t['twitter_image'].'" />
							<em>'.$t['name'].'</em>
							<span class="arrow">></span>
						</a>
					</li>';				
		
		}
	?>
</ul>