<h2>
	<?php if (p('item',false)) { ?>
		Edit '<?php echo $item['item']; ?>'
	<?php } else { ?>
		Add Item To Menu
	<?php } ?>
</h2>

<form method="post" class="direct" action="<?php echo URI.$profile['slug'];?>" enctype="multipart/form-data">
	<input type="hidden" name="truck_id" value="<?php echo $profile['id']; ?>" />
	<input type="hidden" name="form-class" value="menu" />
	<input type="hidden" name="item_id" value="<?php echo p('item',uniqid()); ?>" />
	<ul id="add_menu" class="form">

		<li>
			<label>
				<em>Item <span class="example">(Cheeseburger)</span></em>
				<input type="text" name="item" value="<?php if (!empty($item['item'])) { echo $item['item']; } ?>">
			</label>
		</li>
		<li>
			<label>
				<em>Description <span class="example">(A piece meat on a bun covered with cheese.)</span></em>
				<input type="text" name="description" value="<?php if (!empty($item['description'])) { echo $item['description']; } ?>">
			</label>
		</li>
		<li>
			<label>
				<em>Price <span class="example">(4.25)</span></em>
				<input type="text" name="price" value="<?php if (!empty($item['price'])) { echo $item['price']; } ?>">
			</label>
		</li>
		<li>
			<label>
				<?php if (!empty($item['image'])) { ?>
					<img src="<?php echo $item['image']; ?>" width="200" />
					<input type="hidden" name="oldimage" value="<?php echo $profile['feature_image']; ?>" />
				<?php } ?>
				<em>Image (up to 2000px wide)</em>
				<input type="file" name="uimage" />
			</label>
		</li>
				
		<li class="btns">
			<button type="submit">
				<?php if (p('item',false)) { ?>
					Save
				<?php } else { ?>
					Add
				<?php } ?>
			</button>
			<button type="button" class="cancel close-panel">Cancel</button>
		</li>
	</ul>
	
</form>
