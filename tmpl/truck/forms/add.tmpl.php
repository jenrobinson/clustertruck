<h2>Add Truck</h2>
<form method="post" class="direct" action="<?php echo URI; ?>trucks" enctype="multipart/form-data">
	<input type="hidden" name="do" value="submit">	
	<input type="hidden" name="form-class" value="add-truck" />
			<ul class="form">
				<li>
					<label>
						<em>Truck Name</em>
						<input type="text" name="name" value="" />
					</label>
				</li>
				<li>
					<label>
						<em>Cluster Truck URL <span class="example">http://www.clustertruck.org/<span id="slug-container"><?php echo $newTruckId = uniqid(); ?></span><span id="slug-result" class="good">Available</span></span></em>
						<input id="slug" class="edit-slug" type="text" name="slug" value="<?php echo $newTruckId ?>" />
					</label>
				</li>
				<li>
					<label>
						<em>Short Description</em>
						<input type="text" name="description" value="" />
					</label>
				</li>
				<li>
					<label>
						<em>Twitter ID</em>
						<input type="text" name="twitter" value="" />
					</label>
				</li>
				<li>
					<label>
						<em>Website</em>
						<input type="text" name="website" value="" />
					</label>
				</li>
				<li>
					<label>
						<em>Image (1000px x 475px)</em>
						<input type="file" name="uimage" />
					</label>
				</li>
				<li>
					<label>
						<em>Image Credit</em>
						<input type="text" name="feature_image_attr" value="" />
					</label>
				</li>
			</ul>
		
		<input type="submit" value="Add" />
</form>