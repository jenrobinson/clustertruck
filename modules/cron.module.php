<?php

/**
* Cron Module Class
*
* This class handles logic for automatic functions that are kicked off by a cron job, not by
* a human. An example is that Cluster Truck will send an automatic tweet, every few minutes
* and at the top of the hour, telling followers what trucks are around and also @replying to trucks
* to let them know that some data has changed on the site.
*
*/

class cron extends Fe {

	public function __construct() {
	
		parent::__construct();
	
	}
	
	public function twitter() {
                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
            
		$this->context = 'xhr';
		
		$truckTrack = array();
		
		$tweet = '';
		
		// get 30 oldest tweet
		$tweetList = $this->getTweets();
		print_r($tweetList); 
		if (!empty($tweetList)) { 
		
				// look to aggregate tweets for a truck
				foreach ($tweetList as $t) { 
					
					if (!isset($truckTrack[$t['to']])) { $truckTrack[$t['to']] = 0; }
					$truckTrack[$t['to']]++;
				
				}
				
				// order by most tweets
				ksort($truckTrack);
                                
				$trucks = array_keys($truckTrack);
                                $whichTruckHasMost = $trucks[0];
				
                                // find the first tweet we want to use, then tweet it with total
				foreach ($tweetList as $t) { 
						
						if ($t['to'] == $whichTruckHasMost && !empty($t['tweet'])) { 
						
							$tweet = $this->createTweet($t,$truckTrack,$whichTruckHasMost);
													
							foreach ($tweetList as $thisTweet) { 
						
								if ($thisTweet['to'] == $whichTruckHasMost) { 
								
									$this->markTweetProcessed($thisTweet['id']);
								
								}								
							}
							
                                                        //$this->twitter->statusesUpdate($tweet);
							$resp = $connection->post('statuses/update', array( 'status' => $tweet, 'trim_user' => true, 'include_entities' => true, 'wrap_links' => true));
							break; 
						
						// end check truck match	
						} else if (empty($t['to'])) { 
							
							//send a public tweet
							$tweet = $this->createTweet($t);                                                        
                                                        $resp  = $connection->post('statuses/update', array( 'status' => $tweet, 'trim_user' => true, 'include_entities' => true, 'wrap_links' => true));
							//$this->twitter->updateStatus($tweet);
							break;
						
						} // empty to match for public tweets
				
				} // end foreach for tweets
		
		} // end empty check
		
		
	}
	
	
	
	public function hourlyrecap() { 
	         
                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
               
                $this->context = 'xhr';
		
		$points = $this->getSchedule(false,p('filter',false));
		
		$trucks = array();
		
		$now = strtotime('now') - 25;
		$oneHourFromNow = strtotime('+1 hour') - 25;
                
		foreach ($points as $p) { 
		
			if ($p['time_start'] > $now && $p['time_start'] < $oneHourFromNow) { 
		
				$truckInfo = json_decode($p['truck_info']);		
				$trucks[] = $truckInfo->twitter;
			
			}
		
		}
		//print_r($trucks); die();
		if (!empty($trucks)) { 
		
			$tweet = 'This hour';
			
			// let's construct the tweet
			foreach ($trucks as $t) { 
				
				if (strlen($tweet)+strlen(' @'.$t)+21 < 140) { 
					$tweet .= ' @'.$t; 
				} else { 
					break;
				}
			
			}
                        //$tweet .= ' ' .$this->bitlyLink(URI);	
			
                        $tweet .= ' ' .URI;	                        
                        $resp = $connection->post('statuses/update', array( 'status' => $tweet, 'trim_user' => true, 'include_entities' => true, 'wrap_links' => true));
                                              
			//$this->mytwitter->statusesupdate($tweet);                        
		}
		
	}
	
	
	public function createTweet($t,$truckTrack=false,$whichTruckHasMost=false) {
		
		$tweet = '';
		
		// add at-reply if needed
		if (!empty($t['to'])) { 
			$tweet .= '@'.$t['to'] . ' ';
		}
		
		// add actual tweet text
		$tweet .= html_entity_decode(html_entity_decode($t['tweet']));
		
		// check for total and add to tweet
		if ($truckTrack[$whichTruckHasMost] > 1) { 
			$tweet .= ' (and '.($truckTrack[$whichTruckHasMost] - 1). ' more)';
		}
		
		// try to shorten url
		if (!empty($t['link_url']) && $shortUrl = $this->bitlyLink($t['link_url'])) { 
		
			// add link if needed
			$tweet .= ' ' . $shortUrl;
			
		}
		
		return $tweet;

	
	}

}


?>