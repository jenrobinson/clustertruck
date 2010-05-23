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
			if ( $raw[1] == $this->md5(serialize($raw[0])) AND $raw[0]['ip'] == IP ) {
				
				// reset oauth with the access tokens 
				$to = new EpiTwitter(TWITTER_API_KEY, TWITTER_API_SECRET); 
			
				// set 
				$to->setToken(p('oauth_token'));
				
				// get token
				$token = $to->getAccessToken(array('oauth_verifier' => p('oauth_verifier')));				
					
				// set 
				$to->setToken($token->oauth_token, $token->oauth_token_secret); 									
					
				// make our toke
				$tok = array(
					'oauth_token' => $token->oauth_token, 
					'oauth_token_secret' => $token->oauth_token_secret				
				);
				
				// make it new 
				$e = new EpiTwitter(TWITTER_API_KEY, TWITTER_API_SECRET, $token->oauth_token, $token->oauth_token_secret); 
								
				// ask twitter for their information 
				$resp =  json_decode($e->get('/account/verify_credentials.json', array())->responseText,true);											
				
				//data 
				$data = array(
					'ouath' => $tok,
					'username' => $resp['screen_name']
				);							
								
				// already loged in?
				// they must just be linking
				if ( $this->loged ) { error_log('logged in');
				
					/* update my account 
					$sql = "
						UPDATE `users`
						SET 
							twitter_id = ?,
							twitter_data = ?
						WHERE
							id = ?
					";	
				
					// lets create one
					$r = $this->query($sql,array(
							$resp['id'],
							json_encode($data),
							$this->uid							
						));*/
				
					// log them out and then back in
					$this->logout(false);
				
					// login
					$this->doLogin($resp['id'],json_encode($data),true);
					
				
				}
				else { error_log($resp['id'].' not logged in');
					
					// check our database for this user
					$row = $this->row("SELECT * FROM users WHERE `twitter_id` = ? ",array($resp['id']));
					
					// if we have already created 
					// an account for them
					// no we have to do it now
					if ( $row ) { error_log('account found in db');
					 
						/* sql
						$sql = "
							UPDATE `users`
							SET 
								twitter_data = ?
							WHERE
								id = ?
						";	
					
						// lets create one
						$this->query($sql,array(
								json_encode($data),
								$row['id']							
							));				
					
						*/
						// login
						$this->doLogin($resp['id'],json_encode($data),true);				
					
					}
					else { error_log('no account found in db');
					
						// create them an account
						// names
						$sname = explode(' ',trim($resp['name']));
			
						// first
						$first = trim(array_shift($sname));	
						$last = trim(implode(' ',$sname));
			
						// email
						$email = "twitter.{$resp['id']}@clustertruck.org";
				
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
								$resp['id'],
								json_encode($data),
								md5(md5( uniqid() )),
								md5(md5( uniqid() ))
							));
					
						// login
						$this->doLogin($resp['id'],json_encode($data),true);
					
					}
					
				}
				
				// return
				$url = (isset($raw[0]['return']['url'])?$raw[0]['return']['url']:$this->url('home'));
								
				// all good, we need to redirect
				$this->go($url);
			
			}
		
		}
		else {
		
			// return
			$return = $this->getReturnToken();
			
			// generate a token request
			$to = new EpiTwitter(TWITTER_API_KEY, TWITTER_API_SECRET); 
		
			// get token 
			$tok = $to->getRequestToken();

			// make our cookie
			$data = array( 
						'ip' => IP, 
						'return' => $return, 
						'tok' => array(
							'oauth_token' => $tok->oauth_token, 
							'oauth_token_secret' => $tok->oauth_token_secret				
						)
					);		
		
			// serialize and get a sig
			$sig = $this->md5(serialize($data));
		
			// value
			$val = base64_encode(json_encode( array($data,$sig) ));
		
			// cookie
			setrawcookie('TFX', $val, time()+(60*10), '/', COOKIE_DOMAIN, false, true );			
		
			// send them to the auth url
			$this->go($to->getAuthenticateUrl(null,array('force_login'=>'true','oauth_callback'=>$this->url('twitter-auth'))));
					

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