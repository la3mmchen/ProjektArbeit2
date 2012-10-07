<?php
/**
 * This controller provides the logic needed for the REST-API. 
 **/
class ApiController extends Controller
{    
    /*
     * Components for Logging (file plus hash)
     */
     private $_logPath = 'protected/runtime/'; 
     private $_logFile = 'api.log'; 
    
    /**
    * UserInformation
    **/
    private $user_id;
    private $user_name;
    
    /**
    * Version
    */
	private $version = 1;  # we assume version 1 if $this->version isn't set otherwise
	
	/**
	* Description
	*/ 
	private $description = false; # we assume description is off if $this->description isn't set otherwise
	private $uses = array();
	private $provides = array();
	private $functions = array(
		'get'=>'Options to load user-related objects from backend. see api/<u>get</u>',
		'getContacts'=>'The function <u>getContacts</u> lists all contacts of the authenticated user.',
	);
	
	/*
	* LastUpdate
	*/
	private $lastupdate = false;
	
	/**
	* Some other params
	**/
	private $ddl = false;
	private $source = '';
	
	/**
	* Default descriptions 
	*/ 
	private $textDescriptions = array(
			'username'=>'Name of the user; needed for user-authentication',
			'password'=>'Password of User; needed for user-authentication',
			'lastupdate'=>'Returns a timestamp at which the object was last changed or the time of the last change at a list of objects if set to "true"; form: unix timestamp;',
			'apiversion'=>'Set the targeted version of the webservice; default is 1.',
			'content-type'=> 'application/json',
	);
	
	/*
	* Descriptions 
	*/
	private $outDesc = array ();
	
    /**
     * @return array action filters
     */
    public function filters()
    {
            return array();
    }
    	
	/*
	 * Action to list all available options
	 */
	public function actionIndex()
	{			
		$this->_readParams();
		# default we list available actions
		if (!isset($_GET['action'])) {
					$this->provides['get'] = $this->functions['get'];					
					$this->_returnDescription($this->provides);
		}
	}
	
	/*
     * Action for generic get options
     */  
	public function actionGet()
	{	
		$this->_readParams();
		
		if (!$this->description) # we do not check authentication if the description is to be shown
			$this->_checkAuth();
		
		# default we list available actions
		if (!isset($_GET['action'])) {
					$this->provides['getContacts'] = $this->functions['getContacts'];					
					$this->_returnDescription($this->provides);
		}
		
		switch($_GET['action']) {
			case 'getContacts':
				if ($this->description) {
					$this->uses['content-type'] = "required";
					$this->uses['username'] = "required";
					$this->uses['password'] = "required";
					$this->uses['lastupdate'] = "optional";
					$this->uses['apiversion'] = "optional";
					
					$this->_returnDescription(array('getContacts'=>$this->functions['getContacts']));
				}
				if ($this->version == 2) {
						
				}
				else {
						if ($this->lastupdate) {
							$user = User::model()->findByPk($this->user_id);
							$contactsOfUser = $user->userRel2();
							$maxTimestamp = NULL;
							foreach ($contactsOfUser as $i) {
								$tmpUser = User::model()->findByPk($i->userRel_user1);
								if ($tmpUser->user_changed > $maxTimestamp) 
									$maxTimestamp = $tmpUser->user_changed;
							}
							$this->_sendResponse(200, CJSON::encode($maxTimestamp));
						}
						else {
							$contacts = array();
							$contactsOfUser = User::model()->findByPk($this->user_id);
							if (sizeof($contactsOfUser->userRel2()) > 0) {	
								foreach ($contactsOfUser->userRel2() as $i) {
									$tmpUser = User::model()->findByPk($i->userRel_user1);
									array_push($contacts, $tmpUser);
								}
								$this->_sendResponse(200, CJSON::encode($contacts));
							}
							else {
								$this->_sendResponse(200, CJSON::encode(NULL));
							}
						}
				}
			break;
			default:
				$this->_sendResponse(404);
			}
	}	
	/*
	 * sending response
	 */
	private function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
	{
		// produce log output
		$intValue = date('s');
		$logFile =$this->_logPath."".$intValue."_".$this->_logFile;
		if (file_exists($logFile)) 
			unlink($logFile);
		
		file_put_contents($logFile, time(). " ", FILE_APPEND);
		file_put_contents($logFile, date('r')."\n", FILE_APPEND);
		file_put_contents($logFile, "\n", FILE_APPEND);
		foreach ($_SERVER as $key => $value)  {
				file_put_contents($logFile, '$_SERVER] '.$key . ": ". $value ."\n", FILE_APPEND);
		}
		file_put_contents($logFile, "\n", FILE_APPEND);
		file_put_contents($logFile, '$version: '. $this->version. "\n", FILE_APPEND);
		file_put_contents($logFile, '$lastupdate: '. $this->lastupdate. "\n", FILE_APPEND);
		file_put_contents($logFile, "\n", FILE_APPEND);
		// set the status
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
		header($status_header);
		// and the content type
		header('Content-type: ' . $content_type);
	 
		// pages with body are easy
		if($body != '')
		{
			// send the body
			echo $body;
			exit;
		}
		// we need to create the body if none is passed
		else
		{
			// create some body messages
			$message = '';
	 
			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch($status)
			{
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
					break;
				case 500:
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}
	 
			// servers don't always have a signature turned on 
			// (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
	 
			// this should be templated in a real-world solution
			$body = '
			<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
			<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
				<title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
			</head>
			<body>
				<h1>' . $this->_getStatusCodeMessage($status) . '</h1>
				<p>' . $message . '</p>
				<hr />
				<address>' . $signature . '</address>
			</body>
			</html>';
	 
			echo $body;
			exit;
		}
	}
	
	private function _getStatusCodeMessage($status)
	{
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}
	
	/*
	 * the authentication shouldn't be public
	 */
	private function _checkAuth()
	{
		// Check if we have the USERNAME and PASSWORD HTTP headers set?
		if(!(isset($_SERVER['HTTP_USERNAME']) and isset($_SERVER['HTTP_PASSWORD']))) {
			// Error: Unauthorized
			$this->_sendResponse(401);
		}
		$username = $_SERVER['HTTP_USERNAME'];
		$password = $_SERVER['HTTP_PASSWORD'];
		// Find the user
		$user=User::model()->find('LOWER(user_name)=?',array(strtolower($username)));

		if($user==null) {
			// Error: Unauthorized
			$this->_sendResponse(401);
		} else if(!$user->validatePassword($password)) {
			// Error: Unauthorized
			$this->_sendResponse(401);
		}
		else {
			$this->user_id = $user->user_id;
			$this->user_name = $user->user_name;
		}
	}
		
	/**
	 * extracts all given params from request 
	 */
	private function _readParams() {
		 $this->_handleCORS();
		
		if (isset($_SERVER['HTTP_APIVERSION'])) 
			$this->version = $_SERVER['HTTP_APIVERSION'];
		if (isset($_SERVER['HTTP_LASTUPDATE']) && $_SERVER['HTTP_LASTUPDATE'] == "true") 
			$this->lastupdate = true;
		# some logic: we show always description if a user doen't request application/json
		if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
			if (isset($_SERVER['HTTP_DESCRIPTION']) && $_SERVER['HTTP_DESCRIPTION'] == "true") 
				$this->description = true;
			else 
				$this->description = false;
		}
		else {
			$this->source = "browser";
			$this->description = true;
		}
		return;
	}
	
	/**
	* builds an returns description
	*/
	private function _returnDescription($functionDescription) {
		if (isset($functionDescription) && is_array($functionDescription)) {
			foreach($functionDescription as $key => $value) {
				array_push($this->outDesc, $key.": ".$value);
			}
		}
		foreach ($this->uses as $key => $value) {
				array_push($this->outDesc, $key." [".$value."]: ".$this->textDescriptions[$key]);
			}
		
		
		if ($this->source == "browser")
			$this->_sendBrowserResponse($this->outDesc);
		else 
			$this->_sendResponse(200, CJSON::encode($this->outDesc));
	}
	
	/*
	* sending response to Browser (which is always API-Documentation
	*/
	private function _sendBrowserResponse($body)
	{
		$message = '
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<title>' . Yii::app()->name.' API Documentation</title>
		</head>
		<body>
			<h1>Documentation </h1>';
		$urlAddition = "";
		if(!preg_match('/\/$/', $_SERVER['PATH_INFO'])) {
			$urlAddition = array_pop(preg_split('/\//', $_SERVER['PATH_INFO']));
		}		
		foreach ($body as $i) {
				if(sizeof(preg_split('/\//', $_SERVER['PATH_INFO'])) < 5) { #we get 3 elements on the last level of docu
					if ($urlAddition != "") {
						$i = preg_replace('/<u>(\w*)<\/u>/', '<a href="'.$urlAddition.'/$1/">$1</a>', $i);
					}
					else 
						$i = preg_replace('/<u>(\w*)<\/u>/', '<a href="$1/">$1</a>', $i);
				}
				$message .= '<p>' . $i . '</p>';
		}
		$message .= '<hr />';
		$message .= '
				</body>
				</html>';
 
		echo $message;
		exit;
		
	}	

	/*
	 * Handles CORS
	 */
	 private function _handleCORS() {
			if (isset($_SERVER['HTTP_ORIGIN'])) {
					switch ($_SERVER['HTTP_ORIGIN']) {
							default:
									header('Access-Control-Allow-Origin: *');
									header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
									header('Access-Control-Max-Age: 1000');
									header('Access-Control-Allow-Headers: Content-Type, description, password, username, lastupdate, apiversion, userid, channelid, profileid, profileautomationid, ddl, object, skypename');
									# we don't want the client to cache any requests
									header('Cache-Control: no-cache');
							break;
					}
			}
	 }
}
?>
