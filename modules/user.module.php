<?php

/**
* User Module Class
*
* This handles logic for Twitter login, user account page.
*
*/

class user extends Fe {

	public function __construct() {
	
		parent::__construct();
	
	}
	
	public function login() {
	
		// include main
		include( $this->tmpl('user/login') );
	
	}
	
	
	public function twitter() {
		
		// check for auth token and a cookie with 
		// some oauth information
		$cookie = p('TFX',false,$_COOKIE);
	
		// good
		if ( p('oauth_token') AND $cookie ) {
			
			// no more cookie
			setcookie("TFX",false,time()+1,'/',COOKIE_DOMAIN);
			
			// unpack our cookie
			$raw = json_decode( base64_decode($cookie), true );
		
			// make sure the sig is correct
			if ( $raw[1] == $this->md5(serialize($raw[0])) AND $raw[0]['ip'] == IP ) 
                        {				                 
                                $this->twitter = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $raw[0]['tok']['oauth_token'], $raw[0]['tok']['oauth_token_secret']);
                                $access_token  = $this->twitter->getAccessToken(p('oauth_verifier'));
                                $resp = $this->twitter->get('account/verify_credentials');
                                                  
                               
                                // make our toke
				$tok = array(
					'oauth_token'        => $raw[0]['tok']['oauth_token'], 
					'oauth_token_secret' => $raw[0]['tok']['oauth_token_secret'],
                                        'access_token'       => $acces_token
				);
                                				
				//data 
				$data = array(
					'ouath'     => $tok,
					'username'  => $resp->screen_name
				);							
								
				// already loged in?  they must just be linking
				if ( $this->loged ) 
                                { 
                                        error_log('logged in');
									
					// log them out and then back in
					$this->logout(false);
				
					// login
					$this->doLogin($resp->id,json_encode($data),true);
					
				
				}
				else 
                                { 
                                        error_log($resp->id.' not logged in');
					
					// check our database for this user
					$row = $this->row("SELECT * FROM users WHERE `twitter_id` = ? ",array($resp->id));
					
					// if we have already created an account for them
					if ( $row ) 
                                        {   
                                                error_log('account found in db');					 											
						// login
						$this->doLogin($resp->id,json_encode($data),true);				
					
					}
					else 
                                        { 
                                                error_log('no account found in db');
					
						// create them an account names
						$sname = explode(' ',trim($resp->name));
			
						// first
						$first = trim(array_shift($sname));	
						$last = trim(implode(' ',$sname));
			
						// email
						$email = "twitter.{$resp->id}@clustertruck.org";
				
						// passwor
						$pword = md5(md5( uniqid() ));
				
						// sql
						$sql = "
							INSERT INTO `users`
							SET 
								twitter_id = ?,
								twitter_data = ?,
								api_key = ?,
								api_secret = ?
						";	
					
						// lets create one
						$this->query($sql,array(
								$resp->id,
								json_encode($data),
								md5(md5( uniqid() )),
								md5(md5( uniqid() ))
							));
					
						// login
						$this->doLogin($resp->id,json_encode($data),true);
					
					}
					
				}
				
				// return
				$url = (isset($raw[0]['return']['url'])?$raw[0]['return']['url']:$this->url('home'));
								
				// all good, we need to redirect
				$this->go($url);
			
			}
		
		}
		else {
                        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
                       
                        // Get temporary credentials. 
                        $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

                        // Save temporary credentials to session. 
                        $this->user['oauth_token'] = $token = $request_token['oauth_token'];
                        $this->user['oauth_token_secret'] = $request_token['oauth_token_secret'];

                        // If last connection failed don't display authorization link. 
                        switch ($connection->http_code) 
                        {
                          case 200:
                                $url  = $connection->getAuthorizeURL($token); 
                                $data = array( 
						'ip'  => IP, 						
						'tok' => array(
							'oauth_token'        => $this->user['oauth_token'] , 
							'oauth_token_secret' => $this->user['oauth_token_secret']				
						)
					);		
                        	// serialize and get a sig
                                $sig = $this->md5(serialize($data));
				// value
                                $val = base64_encode(json_encode( array($data,$sig) ));
				// cookie
                                setrawcookie('TFX', $val, time()+(60*10), '/', COOKIE_DOMAIN, false, true );		
                                $this->go($url);
                                //header('Location: ' . $url);                                 
                                break;
                          default:
                                echo 'Could not connect to Twitter. Refresh the page or try again later.';
                                break;
                        }
		}
		
		// just send them home
		$this->go( $this->url('index') );
	
	}
	
	
	public function account() {

		include( $this->tmpl('user/account') );
	
	}
	
	
	public function logout($redi=true) {

		$this->doLogout();

		// go index
		if ( $redi ) {
		
			if (p('url',false)) { 
				
				$url = urldecode(p('url'));
				$this->go( $url );
				
			} else { 
				
				$this->go( $this->url('index') );
			}
			
		}
	
	}
	
	
	


}


?>