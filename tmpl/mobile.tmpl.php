<?php

	$return = $this->getReturnToken();	
	
?><!doctype html> 
<html> 
    <head> 
        <meta charset="UTF-8" /> 
        <title>Cluster Truck Mobile</title> 
        <style type="text/css" media="screen">@import "<?php echo URI; ?>assets/css/mobile.css";</style> 
        <meta content='width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;' name='viewport' />
        <link href='<?php echo URI; ?>assets/images/apple-touch-icon.png' rel='apple-touch-icon' />
		<link href='<?php echo URI; ?>assets/images/apple-touch-icon.png' rel='icon' type='image/png' />
		<link href='<?php echo URI; ?>assets/images/apple-touch-icon.png' rel='apple-touch-startup-image' />
    </head> 
    <body> 
    	
    	<div class="header">
    		
    		<a href="<?php echo URI; ?>m">
    			<img align="left" src="<?php echo URI; ?>assets/images/cluster-truck-logo-small.png" alt="cluster-truck-logo-small" width="" height="40" /> 
    		</a>
    		
    		<h1>
    			<?php echo $this->header; ?>
    		</h1>
    		    		  		
    	</div>
    	
    	<div class="main">
          <?php echo $Body; ?>
        </div>
          
        <div class='footer'>
        	
        	<div class="ad">
        	
        	<script type="text/javascript"><!--
				window.googleAfmcRequest = {
  					client: 'ca-mb-pub-8872687679891829',
  					ad_type: 'text_image',
  					output: 'html',
 					channel: '4388168458',
  					format: '320x50_mb',
  					oe: 'utf8',
  					color_border: '336699',
  					color_bg: 'FFFFFF',
  					color_link: '0000FF',
  					color_text: '000000',
  					color_url: '008000',
				};
				//--></script>
			<script type="text/javascript" 
				src="http://pagead2.googlesyndication.com/pagead/show_afmc_ads.js"></script>

        	</div>
        	
        	<?php if (!$this->loged) { ?>
    			<a class="twitter-signin-btn" href="<?php echo $this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return))); ?>"><img src="<?php echo URI; ?>assets/images/twitter-sign-in.png" alt="Sign-in-with-Twitter-lighter-small" width="" height="" /></a>
    		<?php } else { ?>
    			<a class="logout" href="<?php echo $this->url('logout'); ?>">Logout, <?php echo $this->user['name']; ?></a>
    		<?php } ?>
        	
        	<br /><br />
			View Cluster Truck in:
			<a href="<?php echo URI; ?>standard">Standard</a>
			<div class='copy'>
				Cluster Truck &copy; 2010
			</div>
		</div>             
    </body> 
</html>