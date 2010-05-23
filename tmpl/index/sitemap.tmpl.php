http://www.clustertruck.org/
http://www.clustertruck.org/about
http://www.clustertruck.org/trucks
http://www.clustertruck.org/neighborhoods
<?php

foreach ($trucks as $t) {
	
		echo URI.$t['slug']."\n";
	
}
?>
<?php

foreach ($areas as $t) {
	
		echo URI.'neighborhoods/'.$t['slug']."\n";
	
}
?>