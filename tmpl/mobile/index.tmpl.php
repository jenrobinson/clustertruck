
	<ul class="touch">
	
	<li><a href="<?php echo URI; ?>m/map">Live Schedule</a><span class="arrow">></span></li>
	<li><a href="<?php echo URI; ?>m/trucks">Trucks</a><span class="arrow">></span></li>
	<li><a href="<?php echo URI; ?>m/neighborhoods">Neighborhoods</a><span class="arrow">></span></li>
	
	</ul>
	
	<?php if ($this->loged && $truck) {  $edit = false; ?>
		
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