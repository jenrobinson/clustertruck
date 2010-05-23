<?php 
	$this->metaTitle = $this->truck['name'].' Cluster Truck Widget Demo'; 
?>
<h1><?php echo $this->truck['name']; ?> Widget Demo</h1>

<div style="border:1px solid #888; width: 300px; padding: 20px; float:left; margin-right: 20px; ">
	<script src="http://www.clustertruck.org/widgets/map-<?php echo $this->truck['slug']; ?>.js" type="text/javascript"></script>
</div>

<p>
	Drop this widget code wherever you'd like it to appear:<br />
	<span class="code">
		<?php echo htmlentities('<script src="http://www.clustertruck.org/widgets/map-'.$this->truck['slug'].'.js" type="text/javascript"></script>'); ?>
	</span>
	
</p>