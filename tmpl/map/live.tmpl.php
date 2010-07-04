<?php

	//set meta title
	$this->metaTitle = 'LA Food Truck Map | Cluster Truck';		
?>

<meta http-equiv="refresh" content="300">

<div class="filters">
		<form method="get" class="direct" action="<?php echo URI; ?>">
			Show Me Trucks: 
			<select class="filter" name="filter">
				<option value="today" <?php if (p('filter','') == 'today') { echo 'selected'; } ?>>Today Only</option> 
				<option value="tomorrow" <?php if (p('filter','') == 'tomorrow') { echo 'selected'; } ?>>Tomorrow Only</option> 
				<option value="week" <?php if (p('filter','') == 'week') { echo 'selected'; } ?>>This Week</option>
				<option value="all" <?php if (p('filter','all') == 'all') { echo 'selected'; } ?>>All</option>
			</select>
			<noscript>
				<input type="submit" value="Go" />
			</noscript>
		</form>
	</div>

<div class="header">	
	<h1>Los Angeles Food Truck Map</h1>
	<span class="last-updated">Last updated on <?php echo date("F jS g:ia",$lastUpdated); ?></span>
</div>

<div class="left">
	
	<div id="map">Loading Map...</div> <br />
	
	<script type="text/javascript"><!--
		google_ad_client = "pub-8872687679891829";
		/* CT Live Map Leaderboard */
		google_ad_slot = "6532204906";
		google_ad_width = 728;
		google_ad_height = 90;
		//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>

</div>

<div class="locations">
	
	<h2>Scheduled Trucks</h2>
	<ul>

	<?php
	
		$count = 0;
		$rand = rand(1,5);
	
		foreach ($points as $p) { 
			
			// get truck info from point json
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
	
	// print the point information for javascript
	var points = <?php echo json_encode($points); ?>;

</script>