<h2>
	<?php if ($edit = p('location',false)) { ?>
		Edit '<?php echo $location['name']; ?>'
	<?php } else { ?>
		Add Location To Schedule
	<?php } ?>
</h2>

<form method="post" action="<?php echo URI.$profile['slug'];?>" enctype="multipart/form-data">

	<div class="hidden error-msg"></div>

	<input type="hidden" name="truck_id" value="<?php echo $profile['id']; ?>" />
	
	<?php if ($edit) { ?>
		<input type="hidden" name="location_id" value="<?php echo $location['id']; ?>" />
	<?php } ?>
	
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
				<em>Street Address or Intersection <span class="example">(1234 Some Street, City, State)</span></em>
				<input type="text" name="address" value="<?php if ($edit) { echo $location['address']; } ?>">
			</label>
		</li>
		<li>
			<div id="calendar-container" class="yui-skin-sam"></div>
			<label>
				<em>Date <span class="example">(mm/dd/yy)</span></em>
				<input type="text" name="date" id="date-field" value="<?php if ($edit) { echo date("m/d/y",$location['time_start']); } else { echo date("m/d/y"); } ?>">
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
				<em>Notes <span class="example">(Truck is in front of the bar)</span></em>
				<input type="text" name="notes" value="<?php if ($edit) { echo $location['notes']; } ?>">
			</label>
		</li>
		
	<?php
		if ($this->hasEditAccess() && $this->user['name'] == 'clustertruck') { 
	?>
				<li>
					<label>
						<input type="checkbox" name="tweet_to_truck" /> Tweet To Truck
					</label>
				</li>
				
				<li>
					<label>
						<input type="checkbox" name="tweet_to_public" /> Tweet To Public
					</label>
				</li>
	<?php } ?>
		
		<li class="btns">
			<button type="submit">Save</button>
			<button type="button" class="cancel close-panel">Cancel</button>
		</li>
	</ul>

</form>