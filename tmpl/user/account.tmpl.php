<?php

	if ($this->loged) { 
	
?>
<h1>Welcome, <?php echo $this->user['name']; ?></h1>

<div class="section">
	<h2>Developer Information</h2>
	
	<div class="info">

		<p>Believe it or not, you already have access to all the data stored on Cluster Truck. <br />Using our developer API, you can build applications, widgets ... just about anything. <br />
		You can read all the <a target="_blank" href="http://code.google.com/p/clusttruck-api/wiki/Documentation">documentation here</a>.</p>
		<br />
		<p><em>API Key</em> <?php echo $this->user['api_key']; ?></p>
		<p><em>API Secret</em> <?php echo $this->user['api_secret']; ?></p>
		
		<?php
			 $secret = $this->user['api_secret'];
			 $method = "trucks";
			 $http = "GET";
			 $url = URI."api/v1/trucks?key=".$this->user['api_key'];
			 $sig = md5("{$secret}{$method}{$http}{$url}");
 		?>
		
		<p><em>Sample API Call</em> <a target="_blank" href="<?php echo $url; ?>&sig=<?php echo $sig; ?>">Click To Open</a></p>
		
	
	</div>
</div>

<?php 

	} else { 

		$url = $this->url('twitter-auth',array(),array('return'=>$this->makeReturnToken($return)));
	
		exit( header("Location:".$url) );

	} 
	
?>