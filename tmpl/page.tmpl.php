<?php

	// env urls
	$urls = array(
		'base' => URI,
		'logout' => $this->url('logout'),
		'current' => HOST . $_SERVER['REQUEST_URI']
	);
	
	// return
	
	   $return = $this->getReturnToken();	
	   
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<title><?php echo $this->metaTitle; ?></title>
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.8.0r4/build/reset-fonts-grids/reset-fonts-grids.css"> 
		<link rel="stylesheet" type="text/css" href="<?php echo URI; ?>assets/css/clustertruck.css?v=<?php echo ASSET_VERSION; ?>">
		<link rel="shortcut icon" href="<?php echo URI; ?>favicon.ico" /> 	
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /> 
		<meta content="en-us" http-equiv="Content-Language" /> 
		<meta content="<?php echo $this->metaDescription; ?>" name="description" /> 
		<meta property="og:title" content="<?php echo $this->metaTitle; ?>"/>
		<meta property="og:site_name" content="Cluster Truck"/>
		<meta content="no" http-equiv="imagetoolbar" /> 
		<meta content="width = 1024" name="viewport" /> 
		<meta name="google-site-verification" content="fkAnJycJa2kH2ecOGcQYuMCTwrFhByzPdZ_zr0p86Y0" />
		<meta name="msvalidate.01" content="398253FA7CA10FBE23221F18967CD497" />
		
		<!-- LOAD YUI3 SEED -->
		<script src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script> 
		<script>
		
			// setup the CT module with some variables
			var CT = { 'Load': [], 'Unload': [], 'Store':{}, 'Obj': false, 'Env': { 'fbApiKey':'<?php echo FB_API_KEY; ?>', 'Urls': <?php echo json_encode($urls); ?>, 'AssetVersion': '<?php echo ASSET_VERSION; ?>', 'fb': false } };
			
			CT.add = function(q,o,id) {
                var qs = {'l':'Load','u':'Unload','s':'Store'};
                var h = CT[qs[q]];
                if ( typeof o == 'object' ) {
                    if ( !id ) { id = '_d'; }
                    if ( typeof h[id] == 'undefined' ) { h[id] = {}; }
                    for ( var e in o ) {
                        h[id][e] = o[e];
                    }
                }
                else {
                    h.push(o);
                }
            };
            CT.get = function(id,k) {
				if ( !CT.Store[id] ) {
					return false;
				}
				else if ( !k ) {
					return CT.Store[id];
				}
				else if ( CT.Store[id][k] ) {
					return CT.Store[id][k];
				}
				else {
					return false;
				}				
            }
            CT.execute = function(q) {
                var qs = {'l':'Load','u':'Unload'};       
                var h = CT[qs[q]];             
                for ( var e in h ) {
                    h[e].call();
                    delete(h[e]);
                }

            }
            


			
		</script>
	</head>
	<body class="<?php echo $this->bodyClass; ?>">		
		<div id="doc">
			<div id="hd">
				<div class="logo">Cluster Truck</div>
				<ul class="nav">
					<li><a href="<?php echo URI; ?>">Live Map</a></li>
					<li><a href="<?php echo URI; ?>trucks">Trucks</a></li>
					<li><a href="<?php echo URI; ?>neighborhoods">Neighborhoods</a></li>
					<li><a href="<?php echo URI; ?>about">About</a></li>
					<li>
						<?php 
						
							if ($this->loged) { 
							
								echo '<a href="'.URI.'user">My Account</a>';
								
							}
							
						?>
					<li>
						<div class="twitter-button">
						<?php 
						
							if ($this->loged) { 
									
								echo '<a href="'.$this->url('logout').'">Logout, '.$this->user['name'] .'</a>';
								
							} else {	
						?>
						
						<a class="twitter-signin-btn" href="<?php echo $this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))); ?>">Login Using Twitter</a>
						
						<?php
						
							}
							
						?>
		</div>
					</li>
				</ul>
			</div>
			<div id="bd"><?php echo $Body; ?></div></div>
	
			<div id="ft">
				View Cluster Truck in:
				<a href="<?php echo URI; ?>m/mobile">Mobile</a><br /><br />
				
				Copyright 2010, Cluster Truck<br />
				<a href="http://twitter.com/clustertruck">Follow @clustertruck on Twitter</a> | 
				<a href="http://www.facebook.com/pages/Cluster-Truck/450682440216">'Fan Us' on Facebook</a>
				
				
				
				
			</div>	
	<!-- SERVED FROM SLICEHOST - JR UPDATE -->
			
	<!-- GET YUI2 for PANEL -->
	<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js&2.8.0r4/build/calendar/calendar-min.js"></script> 		
	
	<!-- BASE YUI3 MODULE -->
	<script type="text/javascript" src="<?php echo URI; ?>assets/js/base.js?v=<?php echo ASSET_VERSION; ?>"></script>			
	
<!-- GETCLICKY TRACKING -->
	<script type="text/javascript">
var clicky = { log: function(){ return; }, goal: function(){ return; }};
var clicky_site_id = 172589;
(function() {
  var s = document.createElement('script');
  s.type = 'text/javascript';
  s.async = true;
  s.src = ( document.location.protocol == 'https:' ? 'https://static.getclicky.com' : 'http://static.getclicky.com' ) + '/js';
  ( document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0] ).appendChild( s );
})();
</script>

<!-- GOOGLE ANALYTICS TRACKING -->
	<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-13242813-1");
pageTracker._setDomainName(".clustertruck.org");
pageTracker._trackPageview();
} catch(err) {}</script>
	
	</body>
</html>