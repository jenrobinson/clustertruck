<?php

/**
* Index Module Class
*
* This class handles page mapping for all generic, boring index related pages including About, Sitemap, and Robots.
*
*/

class index extends Fe {

	public function __construct() {
	
		parent::__construct();
	
	}
	
	public function main() {
	
		// include main
		include( $this->tmpl('index/main') );
	
	}
	
	public function about() {
	
		// include main
		include( $this->tmpl('index/about') );
	
	}
	
	
	public function sitemap() {
	
		$trucks = $this->getTrucks();
		$areas = $this->getAreas();
	
		// include main
		include( $this->tmpl('index/sitemap') );
	
	}
	
	public function robots() {
	
		// include main
		include( $this->tmpl('index/robots') );
	
	}


}


?>