<?php
		
	// framework 
    require_once("framework/Global.php");	
    require_once("framework/Framework.php");
    
    // figure out if we need modules too
    $module = p('module','index');

    // call it
    if ( !class_exists($module,true) ) {
        exit("Error Loading Page");
    }    
    
    // call it 
    $obj = new $module();
    
    // dispathc
    $obj->dispatch();
?>