<?php

if(count(get_included_files()) ==1) exit("Direct access not permitted.");

/**
 *
 * Class for authentication
 *
 * Set up a cookie for the user that will ensure that the user is logged in.
 * Or just have it manual check each time. I don't care. 
 *  
 */
 
class AuthenticateUser {
	
	private $pass = 'PASSWORD-REMOVED';
	private $userstatus = false;
	private $maxtime = 3000;
	
	function __construct() {
		session_start();
	}
	
	public function CheckPassword($password){
		if(!$this->CheckForLoginCookie()){
			// attmept to verify password
			if(isset($password) && $password != ''){
				
				$sql = new sqlControl();
				$sql->sqlCommand('SELECT PK FROM AccessLog WHERE IP = :IP AND RESPONSE = "false" AND timestamp > NOW() - INTERVAL 1 MINUTE', array( ':IP' => FilterString($_SERVER['REMOTE_ADDR'])));
				$attempts = count($sql->returnAllResults());
				$sql = '';
				
				if($attempts > 3){
					echo '<div id="error">Too many login attempts, try again in a minute.</div>';
				} else {
					$this->userstatus = password_verify($this->FilterString($password), $this->pass);
					if($this->userstatus){
						// success, create a cookie and login. 
						$this->GenerateLoginCookie();
						$this->userstatus = true;
					}
				}
				
				// log the attempt, regardless of result. 
				$sql = new sqlControl();
				$sql->sqlCommand('INSERT INTO AccessLog (IP, response) VALUES (:IP, :response)', array( ':IP' => FilterString($_SERVER['REMOTE_ADDR']), ':response' => ($this->userstatus ? 'true' : 'false')));
				$sql = '';
			}
		} else {
			$this->userstatus = true;
		}
		return $this->userstatus;
	}
	private function GenerateLoginCookie(){
		if (!isset($_SESSION['LAST_ACTIVITY'])) {
			$_SESSION['LAST_ACTIVITY'] = time();
		} else if (time() - $_SESSION['LAST_ACTIVITY'] > $this->maxtime) {
			// session started more than 5 minutes ago
			session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
			$_SESSION['LAST_ACTIVITY'] = time();
		}
	}
	
	private function CheckForLoginCookie(){
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $this->maxtime)) {		
			// last request was more than 5 minutes ago
			session_unset();     // unset $_SESSION variable for the run-time 
			session_destroy();   // destroy session data in storage	
			return false;	
		} elseif ( !isset($_SESSION['LAST_ACTIVITY']) ) {
			return false;
		} else {
			session_regenerate_id(true);  
			$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
			return true;
		}
	}
	
	public function GeneratePasswordHash($password){
		// take the password and generate a new hash. This will need to be manually reentered to save as "pass"
		 echo password_hash($this->FilterString($password), PASSWORD_DEFAULT) . '<br />';
	}	
	
	private function FilterString($i){
		return filter_var($i, FILTER_SANITIZE_STRING);
	}
	
	public function Logout(){
		session_unset();     // unset $_SESSION variable for the run-time 
		session_destroy();
	}		
}	