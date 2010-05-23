<ul class="touch">
<?php
		
	
		foreach ($neighborhoods as $n) {
						
				echo'
					<li class="truck">
						<a href="'.URI.'m/neighborhoods/'.$n['slug'].'">
							<em>'.$n['name'].'</em>
							<span class="arrow">></span>
						</a>
					</li>';				
		
		}
	?>
</ul>