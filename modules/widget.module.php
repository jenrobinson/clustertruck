<?php

/**
* Widget Module Class
*
* This class handles logic for the embeddable schedule widget. 
* Trucks can embed this widget using a line of Javascript (like an ad) 
* and Cluster Truck will render their current schedule.
*
*/

class widget extends Fe {

	public function __construct() {
	
		parent::__construct();
		
	
	}
	
	public function map() { 
	
		$this->context = 'text';
		
		$slug = p('slug');
		$this->truck = $this->getTruck($slug); 
		$schedule = $this->getSchedule($this->truck['id'],p('filter',false));
				
		include( $this->tmpl('widget/map') );
	
	}
	
	public function demo() { 
		
		$slug = p('slug');
		$this->truck = $this->getTruck($slug); 
				
		include( $this->tmpl('widget/demo') );
	
	}
		
}