<?php

	//set meta title
	$this->metaTitle = 'Food Truck List on Cluster Truck';
	$this->metaDescription = 'Get an up-to-date list of all LA based food trucks.';
	
?><h1>Food Trucks

	<?php
		if ($this->hasEditAccess()) { 
	?>
		 <div class="edit-link">
		 	<a href="<?php echo URI ?>trucks/add" class="button open-panel">Add Truck</a>
		 </div>
	 
<?php } ?>
</h1>

<ul class="trucks">

<?php

	$count = 0;

	foreach ($trucks as $t) {
		
		
		if (empty($t['twitter_image']) && !empty($t['twitter'])) { 
		
			// get the user profile
			
                        $response   = $this->twitter->http('http://api.twitter.com/1/users/show.json?screen_name='.$t['twitter'].'&include_entities=true', 'GET');
                        $user_data  = json_decode($response);                                
                        $twitter    = $user_data->profile_image_url;
                        			
                        if (!empty($twitter)) { $this->query("UPDATE trucks SET twitter_image = ? WHERE slug = ? LIMIT 1",array($twitter,$t['slug'])); }
		}
?>
		<li>
			<a href="<?php echo URI.$t['slug']; ?>"><?php echo $t['name']; ?></a>
		</li>

<?php
		$count++;
		
		if ($count == ceil(count($trucks) / 2)) { echo '</ul><ul class="trucks">'; }
	}

?>

</ul>