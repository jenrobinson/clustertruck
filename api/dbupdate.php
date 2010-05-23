#!/usr/bin/php
<?php

	// run the command to get a dump
	$cmd = "mysqldump -u clusterapi --password='truckin$' --add-drop-table -h db.clustertruck.org clustertruck_db > dump.sql";
	
	echo `$cmd`;

	$db = array(
        'user' => 'cl89278us5ter',
        'pass' => 'cBR222z2eusL4Arj',
        'name' => 'cluster',
        'host' => 'localhost'	
	);

	// no dump
	if ( !file_exists('dump.sql') ) { exit('no db'); };

	# respalce
	$cmd = "mysql -u {$db['user']} --password='{$db['pass']}' {$db['name']} < dump.sql"; 
	
	echo `$cmd`;

	// rm
	unlink("dump.sql");


?>