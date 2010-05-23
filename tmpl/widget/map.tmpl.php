<?php if (p('css',true,$_GET) === true) { ?>

	document.write('<style>.ct-w { font-family: Arial, clean; font-size: 93%; width: <?php echo (int)p("w",300); ?>px; } .ct-w a { color: red; } .ct-w h3 { font-size: 1.25em; font-weight: bold; margin: 10px 0; } .ct-w img { border:1px solid #d3d3d3; -webkit-box-shadow: 0px 0px 15px #999; margin: 0 0 10px; } .ct-w ul { margin: 0; padding: 0; } .ct-w ul li { list-style: none; } .ct-w ul li em { font-weight: bold; font-size: 1.2em; font-style: normal; margin: 0 0 15px; display: block;  } .ct-w ul li p { font-size: .88em; margin: 0 !important; padding:0;  } .ct-w ul li p a { text-decoration: none; font-style:oblique; font-size: 11px; } .ft { text-align: right; font-size: 11px; color: #333; font-style:oblique; } .ct-w ul li span.c { float: left; margin: 0 15px 0 0; padding: 7px 0 15px; color: #888; } .ct-w .none { color: #999; font-size: 1.25em; } </style>');

<?php } ?>

document.write('<div class="ct-w">'); 

<?php if (p('title',true,$_GET) === true) { ?>

	document.write('<h3><?php echo $this->truck['name']; ?> Schedule</h3>');
	
<?php } 

	if (!empty($schedule)) { ?>

		document.write('<img class="map" src="http://maps.google.com/maps/api/staticmap?size=<?php echo (int)p("w",300); ?>x<?php echo (int)round(p("w",300)/1.5); ?>&maptype=roadmap<?php 
		
			$count = 'A';
			
			foreach ($schedule as $s) { 
				
				$mapsLink = '';
				
				echo '&markers=color:red|label:'.$count.'|'.urlencode($s['lat']).','.urlencode($s['lon']);
				
				$count++;
				
			}
			
		?>&sensor=false&key=<?php echo GOOGLE_MAPS_API; ?>" />');
		
		document.write('<ul>');
		
		<?php 
		
			$count = 'A';
			
			foreach ($schedule as $s) { 
				
				$mapsLink = '';
				
				echo 'document.write(\'<li><span class="c">'.$count.'</span><p>'.$s['datetime'].' <a target="_blank" href="http://maps.google.com/maps?q='.urlencode($s['address']).'">Map &#187;</a></p><em>'.$s['name'].'</em></li>\');';
				
				$count++;
				
			}
			
		?>
		
		document.write('</ul>');

<?php } else { ?>

	document.write('<p class="none">Sorry, check back soon for an updated schedule.</p>');

<?php } ?>

document.write('<div class="ft">Powered by <a href="http://www.clustertruck.org/<?php echo $this->truck['slug']; ?>">Cluster Truck</a></div>');

document.write('</div>');