<?php

	//set meta title
	$this->metaTitle = 'Neighborhood List on Cluster Truck';
	$this->metaDescription = 'Get an up-to-date list of all neighborhoods in LA where you might find food trucks.';
	
?><h1>Neighborhoods

	<?php
		if ($this->hasEditAccess()) { 
	?>
		 <div class="edit-link">
		 	<a href="<?php echo URI ?>trucks/add" class="button open-panel">Add Neighborhood</a>
		 </div>
	 
<?php } ?>
</h1>

<ul class="trucks">

<?php

	$count = 0;

	foreach ($areas as $t) {
	
?>
		<li>
			<a href="<?php echo URI.'neighborhoods/'.$t['slug']; ?>"><?php echo $t['name']; ?></a>
		</li>

<?php
		$count++;
		
		if ($count == ceil(count($areas) / 2)) { echo '</ul><ul class="trucks">'; }
	}

?>

</ul>

<p style="clear:both">&#187; Don't see your neighborhood? <a href="mailto:scott@clustertruck.org">Email Scott</a> and request it.</p>