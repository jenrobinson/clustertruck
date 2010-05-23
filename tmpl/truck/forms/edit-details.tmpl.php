<h2>Edit Truck Details</h2>
<form method="post" class="direct" action="<?php echo URI.$profile['slug'];?>" enctype="multipart/form-data">
	<input type="hidden" name="do" value="submit">	
	<input type="hidden" name="form-class" value="profile" />
		<input type="hidden" name="oldimage" value="<?php echo $profile['feature_image']; ?>" />
			<ul class="form">
				<li>
					<label>
						<em>Truck Name</em>
						<input type="text" name="name" value="<?php echo $profile['name']; ?>" />
					</label>
				</li>
				<li>
					<label>
						<em>Description</em>
						<input type="text" name="description" value="<?php echo $profile['description']; ?>" />
					</label>
				</li>
				<li>
					<label>
						<em>Twitter ID</em>
						<input type="text" name="twitter" value="<?php echo $profile['twitter']; ?>" />
					</label>
				</li>
				<li>
					<label>
						<em>Website</em>
						<input type="text" name="website" value="<?php echo $profile['website']; ?>" />
					</label>
				</li>
				<li>
					<label>
						<?php if (!empty($profile['feature_image'])) { ?>
							<img src="<?php echo $profile['feature_image']; ?>" width="200" />
						<?php } ?>
						<em>Big Image (1000px x 475px)</em>
						<input type="file" name="uimage" />
					</label>
				</li>
				<li>
					<label>
						<em>Image Credit</em>
						<input type="text" name="feature_image_attr" value="<?php echo $profile['feature_image_attr']; ?>" />
					</label>
				</li>
			</ul>
		
		<input type="submit" value="Save Details" />
</form>