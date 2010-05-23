<?php

	//set meta title
	stripos($profile['name'],'truck') > -1 ? $truck = '' : $truck = ' Truck';
	
	$this->metaTitle = $profile['name'] . $truck;
	$this->metaDescription = 'Get the latest food truck schedules, menus, and comments for '. $profile['name'];
	$return = $this->getReturnToken();
	
	//set default photo
	if ( empty($profile['feature_image']) ) {
		$profile['feature_image'] = 'http://cdn.bitsybox.com/79384167418d92bc7e1c9da583f65970.jpg';
	}
			
?>

<?php if (!$this->loged) {  ?>
	<div class="reminder">
		Are you <?php echo $profile['twitter']; ?>? <a class="twitter-signin-btn" href="<?php echo $this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))); ?>">Click here to login</a> and edit this page.
	</div>
<?php } ?>

<?php

	if ($this->loged && $formError) { 
		
		echo '<div class="error-msg">'.$errorMsg.'</div>';
	
	}
	
?>

<div class="profile" style="background: #333 url('<?php echo $profile['feature_image']; ?>') no-repeat;">
	<h1>
	
	<?php if (!empty($profile['website'])) { ?>
		<a target="_blank" class="website" href="<?php echo $profile['website']; ?>" >
	<?php } ?>
	
		<?php echo $profile['name']; ?>
	
	<?php if (!empty($profile['website'])) { ?>
		</a>
	<?php } ?>
	
	<?php
		if ($this->hasEditAccess()) { 
	?>
		 <div class="edit-link">
		 	<a href="<?php echo URI . $profile['slug']; ?>/edit-details" class="button open-panel">Edit Truck Info</a>
		 </div>
	 
<?php } ?>
	
	</h1>	
	<?php
		if (!empty($schedule)) { 
	?>
		<div class="location">
			<h3>Next Scheduled Spot:</h3>
			<div id="map"></div>
		</div>
	<?php } ?>
	<p class="history">
		<?php echo $profile['description']; ?> <br />
		
		<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo SELF; ?>&amp;show_faces=false&amp;width=300&amp;colorscheme=dark" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:30px; margin-top: 5px;" allowTransparency="true"></iframe>
		
	</p>
	
	<?php
		
			//show twitter icon if they have one (probably)
			if (!empty($profile['twitter'])) { 
				
				echo '<a target="_blank" class="twitter" href="http://twitter.com/'.$profile['twitter'].'"><img src="'.URI.'assets/images/Twitter_32x32.png" alt="Twitter_32x32" width="" height=""/></a>';
			
			}
			
		?>
</div>	

<?php 
	if (!empty($profile['feature_image_attr'])) { 
?>
		<div class="photo-credit">Photo By <?php echo $this->parsePhotoCredit($profile['feature_image_attr']); ?></div>				
<?php } ?>



<!--SCHEDULE-->
<h2 id="schedule">
	<?php echo $profile['name']; ?> Schedule
	<?php
		if ($this->loged) { 
	?>
			 <a href="<?php echo URI . $profile['slug']; ?>/add-location" class="button open-panel">Add To Schedule</a>
		 
	<?php } ?>

</h2> 

<?php if (!$this->loged && !empty($schedule)) { 

		
		echo '<span class="big">You can <a href="'.$this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))).'">login</a> and add to '.$profile['name'].'\'s schedule and menu yourself.</span>';
			
		}
			
?>

<ul class="schedule clearfix">
	
	<?php
		
		$count = 0;
		
		if (empty($schedule)) { 
			
			if ($this->loged) {
				echo '<div class="big">No scheduled times available on Cluster Truck.<br /> You can <a class="open-panel" href="'.URI . $profile['slug'].'/add-location">add to '.$profile['name'].'\'s schedule</a> yourself, or check <a target="_blank" class="twitter" href="http://twitter.com/'.$profile['twitter'].'">Twitter</a> for the latest.</div>';
			} else { 
				echo '<div class="big">No scheduled times available on Cluster Truck.<br /> You can <a href="'.$this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))).'">login</a> and add to '.$profile['name'].'\'s schedule yourself, or check <a target="_blank" class="twitter" href="http://twitter.com/'.$profile['twitter'].'">Twitter</a> for the latest.</div>';
			}
			
		} 
		
		foreach ($schedule as $s) { 
			
			
			echo '<li>
					<h3>'.date("D M j, g:ia",$s['time_start']).' - '.date("g:ia",$s['time_stop']).'</h3>
					<h4>'.$s['name'].'</h4>
					<h5>'.$s['address'].'</h5>
					';
					
			//edit / delete buttons for admins
				if ($this->hasEditAccess() || ($this->loged && $this->user['name'] == $s['added_by']) ) {
						echo '<br/>
						<a id="'.$s["id"].'" class="button open-panel edit-location" href="'.URI.$slug.'/edit-location/'.$s["id"].'">Edit</a>
						<a class="button" href="?deleteLocation='.$s['id'].'">Delete</a>';
				}
			
			echo '</li>';
			
			$count++;
		}
		
		if ($count % 2 != 0) { echo '<li>&nbsp</li>'; }
			
	?>
	
</ul>

<!--MENU-->
<h2>
	<?php echo $profile['name']; ?> Menu
	<?php
		if ($this->hasEditAccess() && $this->loged) {
	?>
			 <a href="<?php echo URI . $profile['slug']; ?>/add-menu" class="button open-panel">Add To Menu</a>
		 
	<?php } ?>
</h2>
<ul class="menu">
	
	<?php 	
	
		$menuItems = json_decode($profile['menu'],true);
		$count = 0;
		
		if (empty($menuItems)) { 
		
			if (!$this->loged) { 

				echo '<span class="big">You can <a href="'.$this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))).'">login</a> and add to '.$profile['name'].'\'s menu yourself.</span>';
			
			} else { 
				
				echo '<span class="big">Menu has not been provided yet.</span>';
			
			}
		
		} else { 
		
			foreach ($menuItems as $m) {
						
				$image = $m['image'];
				$name = $m['item'];
				$description = $m['description'];
				$price = $m['price'];
				if (isset($m['added_by'])) { 
					$added_by = $m['added_by'];
				} else { 
					$added_by = '';
				}
			
		?>	
			
			<li>
				
				<div class="left">
					<?php if (!empty($image)) { ?>
						<img src="<?php echo $image; ?>" alt="Photo of <?php echo $name; ?> from <?php echo $profile['name']; ?>"  height="100"/>
					<?php } else { ?>
						<img src="<?php echo URI; ?>assets/images/no-image.jpg" alt="Image not available"  width="175" height="100"/>
					<?php } ?>
					<?php if ($this->hasEditAccess() || $added_by == $this->user['name']) { ?>
						<br/>
						<a id="<?php echo $m['id']; ?>" class="button open-panel edit-menu" href="<?php echo URI.$slug;?>/edit-menu/<?php echo $m['id'];?>">Edit</a>
						<a class="button" href="?deleteMenuItem=<?php echo $m['id'];?>">Delete</a>
					<?php } ?>
				</div>
				
				<em>
					<?php echo $name; ?>
					<?php if (!empty($price)) { ?>
						<span class="price">$<?php echo $price; ?></span>
					<?php } ?>
					</em>
				<p>
					<?php echo $description; ?> 
					
					<?php 
						
						if (!empty($added_by) && $added_by != $profile['twitter']) { 
						
							echo ' <span class="added-by">(<a href="http://twitter.com/'.$added_by.'" rel="nofollow">Contributed by '.$added_by.'</a>)</span>';
							
						}
						
					?>
					
					
				</p>
			</li>
			
		<?php
		
			$count++;
			
			}
			
		}

		if ($count % 2 != 0) { echo '<li>&nbsp</li>'; }
			
	?>
			
</ul>

<br />
<h2><?php echo $profile['name']; ?> Widget</h2>
<p>
	Display <?php echo $profile['name']; ?>'s schedule on your own website. <br />You can <a href="<?php echo URI . $profile['slug']; ?>/widget">view a demo</a> of the widget in action. To use it, just drop this widget code wherever you'd like it to appear:<br />
	<span class="code">
		<?php echo htmlentities('<script src="http://www.clustertruck.org/widgets/map-'.$profile['slug'].'.js" type="text/javascript"></script>'); ?>
	</span>
	
</p>

<!--h2><?php echo $profile['name']; ?> Comments / Reviews</h2>

<div class="comments">

	<div id="disqus_thread"></div><script type="text/javascript" src="http://disqus.com/forums/clustertruck/embed.js"></script><noscript><a href="http://disqus.com/forums/clustertruck/?url=ref">View the discussion thread.</a></noscript><a href="http://disqus.com" class="dsq-brlink">blog comments powered by <span class="logo-disqus">Disqus</span></a>

</div-->

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">
	
	var points = <?php if (!empty($schedule)) { echo json_encode(array($schedule[0])); } else { echo 'false'; }?>;

</script>
