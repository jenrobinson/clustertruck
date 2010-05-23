<?php
	
	// checks if the server name has the word DEV, ALPHA, or AWESOME in it - if so it's dev
	if (strpos($_SERVER['SERVER_NAME'],'dev')!==false || strpos($_SERVER['SERVER_NAME'],'alpha')!==false || strpos($_SERVER['SERVER_NAME'],'awesome')!==false) { 
		$dev = true;
	} else { 
		$dev = false;
	}
	
    // in dev
	define("DEV",$dev);

		// dev
		if ( DEV ) {
		    error_reporting(E_ALL);
		    ini_set("display_errors",1);		
		}

	// asset version
	// if dev we make it time() to break cache
	define("ASSET_VERSION", (DEV===true?time():10 ) );

    // get the file name
    $path = explode("/",$_SERVER['SCRIPT_FILENAME']);

    // get the root
    define("ROOT", implode("/",array_slice($path,0,-1))."/");
    
    if (DEV) { 
       define("FRAMEWORK_ROOT","/home/blackstripe/dev.clustertruck.org/clustertruck/framework/");
    } else { 
       define("FRAMEWORK_ROOT","/home/blackstripe/clustertruck.org/clustertruck/framework/");
    }

    // need to get base tree
    $uri = explode('/',$_SERVER['SCRIPT_NAME']);  

    // define 
    define("HTTP_HOST",		 $_SERVER['HTTP_HOST']);
    define("HOST",      	 "http://".$_SERVER['HTTP_HOST']);
    define("URI",      		 HOST.implode("/",array_slice($uri,0,-1))."/");
    define("COOKIE_DOMAIN",	 false);
    define("IP",			 $_SERVER['REMOTE_ADDR']);
    define("SELF",			 HOST.$_SERVER['REQUEST_URI']);    

	// helpdes
	define("HOUR",(60*60));
	define("DAY",(60*60*24));
	
	///////////////////////// PASSSSWORDS /////////////////////////
	
	// facebook stuff
	define("FB_API_KEY","");
	define("FB_API_SECRET","");
	
	// twitter
	define("TWITTER_API_KEY","6WohpwUxYxHlabeow4lycA");
	define("TWITTER_API_SECRET","EwOEG08Hz4hDkyBAMNNfWFUdeiFFumahsfGJB7QaI");
	
	// bitly
	define("BITLY_LOGIN",'clustertruck');
	define('BITLY_KEY','R_08415686546cbe7e68a089001706c119');
	
	// dev twitter acct
	if ( DEV ) {
		define("TWITTER_USER","clustertruckdev");
		define("TWITTER_PASS","clu$tertruck");
	} else { 
		define("TWITTER_USER","clustertruck");
		define("TWITTER_PASS","clu$tertruck");
	}
	
	// google
	define("GOOGLE_MAPS_API",'');
	
	//s3
	define('S3_ACCESS','');
	define('S3_SECRET','');
	define('S3_BUCKET', '');
	
	///////////////////////////////////////////////////////////////////////////////
	
	

	// autoload
	function __autoload($name) { 
        if ( file_exists(FRAMEWORK_ROOT."{$name}.php") ) {
            require_once( FRAMEWORK_ROOT . "{$name}.php" );
        }
        else if (file_exists(ROOT."classes/{$name}.class.php")) {
			require_once(ROOT. "classes/{$name}.class.php");        
        }
        else if (file_exists(ROOT."modules/{$name}.module.php")) {
        	require_once(ROOT. "modules/{$name}.module.php");
        }
		else {
			exit("Requested class {$name} does not exist");
		}
	}	

	// monet
	date_default_timezone_set("America/Los_Angeles");

	////////////////////////////////
	///  @brief config
	////////////////////////////////
	class Config {	
	
		// config
		private static $config = array(
			'db' => array(
			    'user' => 'clusteruser',
			    'pass' => '',
			    'name' => 'clustertruck_db',
			    'host' => 'db.clustertruck.org'
			),
			'db_prod' => array(
			    'user' => 'clusteruser',
			    'pass' => '',
			    'name' => 'clustertruck_db',
			    'host' => 'db.clustertruck.org'
			),
			'db_dev' => array(
			    'user' => 'clusteruser',
			    'pass' => '',
			    'name' => 'clustertruck_db',
			    'host' => 'db.clustertruck.org'
			),
			'pages' => array(
			
			)
		);	
		
		////////////////////////////////
		/// @breif get a predefined config
		////////////////////////////////		
		public static function get($var) {
			
			// config
			$config = self::$config;

			// what evn
			$var_pf = $var . (DEV?'_dev':'_prod');
			
			// var
			if ( isset($config[$var_pf]) ) {
				return $config[$var_pf];
			}

			if ( isset($config[$var]) ) {
				return $config[$var];
			}
			
		}
	
		////////////////////////////////
		/// @breif set a config val
		////////////////////////////////
		public static function set($var,$val) {
			self::$config[$var] = $val;		
		}
					
	
		////////////////////////////////
		/// @breif get a url
		////////////////////////////////		
		public static function url($key,$data=false,$params=false) {
			
			// key = 'slef'
			if ( $key == 'self' ) {
				return SELF;
			}
			
			// define our urls
			$pages = self::$config['pages'];
			
			// get a url
			if ( array_key_exists($key,$pages) ) {
				$url = $pages[$key]; 
			}
			else {
				$url = $key;
			}
			
			
			// repace toeksn
			if ( is_array($data) ) {
							
				foreach ( $data as $k => $v ) {
					if ( is_string($k) AND is_string($v) ) {
						// check for * in key
						if ( substr($k,0,1) != '*' ) {
							$v = strtolower(preg_replace(
								array("/[^a-zA-Z0-9\-\/]+/","/-+/"),
								"-",
								$v
							));						
						}
						else {
							$k = substr($k,1);
						}
						
						// url
						$url = str_replace('{'.$k.'}',$v,$url);
					}					
				}
			}
			
			// clean up
			$url = preg_replace("/\{[a-z]+\}\/?/","",$url);
			
			// params
			if ( is_array($params) ) {
				$p = array();
				foreach ( $params as $k => $v ) {
					$p[] = "{$k}=".urlencode($v);
				}
				$url .= (strpos($url,'?')==false?'?':';').implode(';',$p);
			}
			
			// give back
			if (stripos($url,"http://") === 0) { 
				return $url;
			} else { 
				return URI . $url;
			}
		
		}
	
	
	}


	/**
	 * global paramater function
	 * @method	p
	 * @param	{string}	key name
	 * @param	{string} 	default value if key != exist [Default: false]
	 * @param	{array}		array to look in [Default: $_REQUEST]
	 * @param   {string}    string to filter on the return
	 */
	function p($key,$default=false,$array=false,$filter=false) {
	
		// check if key is an array
		if ( is_array($key) ) {
		
			// alawys 
			$key = $key['key'];
			
			// check for other stuff
			$default = p('default',false,$key);
			$array = p('array',false,$key);
			$filter = p('filter',false,$key);
			
		}
		
		// no array
		if ( !$array OR !is_array($array) ) {
			$array = $_REQUEST;
		}
	
		// check 
		if ( !array_key_exists($key,$array) OR $array[$key] == "" OR $array[$key] == 'false' ) {
			return $default;
		}
		
		// filter ?
		if ( $filter ) {
			$array[$key] = preg_replace("/[^".$filter."]+/","",$array[$key]);
		}
		else if ( is_string($array[$key]) ) {
			$array[$key] = strip_tags(htmlentities($array[$key],ENT_QUOTES,'utf-8',true));
		}
	
		// reutnr
		return $array[$key];
	
	}
	
		// p raw
		function p_raw($key,$default=false,$array=false) {
			return p($key,$default,$array,'.*');
		}
	
	/**
	 * global path function 
	 * @method	pp
	 * @param	{array}		position (index) in path array
	 * @param	{string}	default 
	 * @param	{string}	filter
	 * @return	{string}	value or false
	 */
	function pp($pos,$default=false,$filter=false) {
		
		// path 
		$path = explode('/',trim(p('path'),'/'));
		
		// yes?
		if ( count($path)-1 < $pos OR ( count($path)-1 >= $pos AND $path[$pos] == "" ) ) {
			return $default;
		}
	
		// filter
		if ( $filter ) {
			$path[$pos] = preg_replace("/[^".$filter."]+/","",$path[$pos]);
		}
		
		// give back
		return $path[$pos];
	
	}	

	function utctime() {
	
		// datetime
		$dt = new DateTime('now',new DateTimeZone('UTC'));		
		
		// return utctime
		return $dt->getTimestamp();
	
	}
	
	function plural($str,$count) {
		if ( is_array($count) ) { $count = count($count); }
		
		if ( substr($str,-1) == 'y' AND $count > 1 ) {
			return substr($str,0,-1)."ies";
		}
		return $str . ($count!=1?'s':'');
	}
	
	function ago($tm,$rcs = 0) {
	    $cur_tm = utctime(); $dif = $cur_tm-$tm;
	    $pds = array('second','minute','hour','day','week','month','year','decade');
	    $lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);
	    for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
	   
	    $no = floor($no); if($no <> 1) $pds[$v] .='s'; $x=sprintf("%d %s ",$no,$pds[$v]);
	    return $x . ' ago';
	}

?>