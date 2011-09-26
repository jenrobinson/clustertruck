<?php

	$this->header = $this->truck['name'];

?>

<h2>Overview</h2>
<p class="text">
	<?php echo $this->truck['description']; ?>
</p>

<?php if (!empty($this->truck['feature_image'])) { ?>
	<h2>Photo</h2>
	<p class="text">
		<a href="<?php echo $this->truck['feature_image']; ?>">
			<img width="100%" src="<?php echo $this->truck['feature_image']; ?>" />
		</a>
	</p>
<?php } ?>

<h2>Schedule</h2>
<ul class="touch">
<?php
		
		if (empty($schedule)) { 
			
			echo '<p class="text">None Available</p>';
		
		}
		
		foreach ($schedule as $s) { 
			
			$mapsLink = URI.'m/map/'.$s['id'];
						
			$truck = json_decode($s['truck_info'],true);
						
				echo'
					<li class="location">
						<a target="_blank" href="'.$mapsLink.'">
							<img class="icon" idth="48" height="48" src="'.$truck['twitter_image'].'" />
							<em>'.$truck['name'].' at '.$s['name'].'</em>
							<p>'.$s['datetime'].'</p>
							<span class="google">View Map &#187;</span>
						</a>';
						
					if ($this->loged && $this->user['name'] == $truck['twitter']) { 
					
						echo '<a onclick="return confirm(\'Are you sure you want to remove '. str_replace("'","\'",$s['name']) .'?\')" class="edit" href="?deleteLocation='.$s['id'].'">Remove</a>';
					
					}
				
				echo '</li>';				
		
		}
	?>
</ul>


<?php if ($this->loged && $this->user['name'] == $this->truck['twitter']) { $edit = false; ?>

		<h2>Add To Your Schedule</h2>
				
			<form method="post" action="<?php echo URI.'m/trucks/'.$truck['slug'];?>" enctype="multipart/form-data">
			
				<input type="hidden" name="truck_id" value="<?php echo $truck['id']; ?>" />
				<input type="hidden" name="form-class" value="location" />
			
				<ul id="add_location" class="form">
					<li>
						<label>
							<em>Location Name</em>
							<input type="text" name="location_name" value="<?php if ($edit) { echo $location['name']; } ?>">
						</label>
					</li>
					<li>
						<label>
							<em>Street Address, City, State</span></em>
							<input type="text" name="address" value="<?php if ($edit) { echo $location['address']; } ?>">
						</label>
					</li>
					<li>
						<div id="calendar-container" class="yui-skin-sam"></div>
						<label>
							<em>Date <span class="example">(mm/dd/yy)</span></em>
							<input type="text" name="date" id="date-field" value="<?php echo date('m/d/y'); ?>">
						</label>
					</li>
					<li>
						<label>
							<em>Time Start <span class="example">(7:00pm)</span></em>
							<input type="text" name="time_start" value="<?php if ($edit) { echo date("g:ia",$location['time_start']); } ?>">
						</label>
					</li>
					<li>
						<label>
							<em>Time End <span class="example">(10:00pm)</span></em>
							<input type="text" name="time_end" value="<?php if ($edit) { echo date("g:ia",$location['time_stop']); } ?>">
						</label>
					</li>
					<li>
						<label>
							<em>Notes <span class="example">(Find us at the corner, near the fire hydrant)</span></em>
							<input type="text" name="notes" value="<?php if ($edit) { echo $location['notes']; } ?>">
						</label>
					</li>
					
					<li class="btns">
						<button type="submit">Add</button>
					</li>
				</ul>
			
			</form>
			
<?php } ?>