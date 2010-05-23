<h2>
	<a href="<?php echo URI.'m/trucks/'.$truck['slug']; ?>">
		<img class="icon" idth="48" height="48" src="<?php echo $truck['twitter_image']; ?>" />
	</a>
	<a href="<?php echo URI.'m/trucks/'.$truck['slug']; ?>"><?php echo $truck['name'].'</a> at '.$schedule[0]['name']?> 
	<br />
	<em><?php echo $schedule[0]['datetime']; ?></em> 
</h2>

<a target="_blank" href="<?php echo 'http://maps.google.com/maps?q='.urlencode($schedule[0]['address']); ?>">
	<img width="100%" src="http://maps.google.com/maps/api/staticmap?center=<?php echo urlencode($schedule[0]['address']); ?>&zoom=14&size=450x350&maptype=roadmap
&markers=color:red|<?php echo urlencode($schedule[0]['lat']); ?>,<?php echo urlencode($schedule[0]['lon']); ?>&sensor=false&key=<?php echo GOOGLE_MAPS_API; ?>" />
</a>

<strong class="caption">Click Map To View in Google Maps</strong><br /><br />
