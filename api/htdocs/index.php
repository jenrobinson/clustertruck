<?php

	// checks if the server name has the word DEV, ALPHA, or AWESOME in it - if so it's dev
	if (strpos($_SERVER['SERVER_NAME'],'dev')!==false || strpos($_SERVER['SERVER_NAME'],'alpha')!==false || strpos($_SERVER['SERVER_NAME'],'awesome')!==false) { 
		$dev = true;
	} else { 
		$dev = false;
	}
   	
   	// in dev
	define("DEV",$dev);	
    define("ROOT","/app/www/");
   	
    // global
    require(ROOT."framework/Global.php");
    require(ROOT."framework/Framework.php");
    
    error_reporting(0 ^ E_NOTICE);
    ini_set("display_errors",0);

	// tz
	date_default_timezone_set("America/Los_Angeles");

    // what module
    $module = p('module');
    	
	if ( !$module OR !$module OR !file_exists(ROOT."api/share/modules/{$module}.module.php") ) {
        Api::errorDoc("Bad Request",403,1);
    } else { 
    	require(ROOT."api/share/classes/Api.class.php");
    	require(ROOT."api/share/modules/{$module}.module.php");
    }
    
    // need a class
    if ( !class_exists($module,true) ) { 
        Api::errorDoc("Bad Request",403,2);
    }
    
    // get key and sig    
    $args = Api::init();
    
    // what up
    if ( !$args['key'] OR !$args['sig'] OR strlen($args['key']) != 32 OR strlen($args['sig']) != 32 ) { 
        Api::errorDoc("Bad Request",403,3);
    }
    
    // method
    $method = $args['method'];
    
    // is valid method
    if ( !in_array($args['method'],get_class_methods($module)) ) { 
        Api::errorDoc("Bad Request",403,4);
    }
    
    // call it
    $obj = new $module();

?>