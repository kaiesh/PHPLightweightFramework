<?
/*************
* A sample class that demonstrates how to extend the Core
**************/

require_once("kkMailMessage.php");
require_once("Core.php");
require_once("DatabaseObject.php");

class myAppCore extends Core{
    /*
        Status messages as consistent with the application
    */

	private $objectStore; //generic storage location for objects
	private $footprintCalibration;
	private $paramStore; //Variable storage
	private $runningLevel; //mode of operation (dev/test/prod)

	const SITE_ROOT = "http://the.website.com/";
	const LIVESTATUS_LIVE = 1;
	const LIVESTATUS_OLD = 0;
	const LIVESTATUS_DELETED = 3;

	const LOCALHOST = 1;
	const DEVELOPMENT = 2;
	const TESTING = 3;
	const PRODUCTION = 4;

    const FAILED = "FAIL";
    const SUCCESS = "OK";
    const RETRY = "RETRY";


	/* Constructor */
	function myAppCore(){

		$mode = myAppCore::PRODUCTION;  /* Set this manually in the core */

		$this->footprintCalibration = true; //by default, log stuff
		switch ($mode){
				case myAppCore::TESTING:
				case myAppCore::PRODUCTION:
					$this->runningLevel = $mode;
					$this->setObjectPath("/the/website/online/web/content/objects");
					break;
				default: //assume localhost
					$this->runningLevel = myAppCore::LOCALHOST;
					$this->setObjectPath("/var/www/objects");
				break;
		}
	}

	/* Destructor */
	function __destruct() {
		if ($this->footprintCalibration){
			$this->footprinting();
		}
	}

	/* Footprinter */
	private function footprinting(){
		//Put whatever footprint method you want to here
		//if you want you can store number of queries, load time, browser details, etc etc into a db when the page terminates
	}

	/* Connect DB command */
	function connectDB(){
		switch ($this->runningLevel){
			case myAppCore::LOCALHOST:
				$db_add = "localhost";
				$db_user = "yourdbuser";
				$db_pass = "yourpasswordhere";
				$db_name = "yourdbname";
				break;
			case myAppCore::TESTING:
			case myAppCore::DEVELOPMENT:
			case myAppCore::PRODUCTION:
				$db_add = "the.webaddress.com";
				$db_user = "yourdbuser";
				$db_pass = "yourpasswordhere";
				$db_name = "yourdbname";
				break;
			default:
				throw new Exception ("Core instantiation error. Mode of operation not correctly defined");
				break;
		}
		$this->establishDB($db_add, $db_user, $db_pass, $db_name);
	}

	/* Core variable storage */
	function setParameter($paramName, $paramValue){
		$this->paramStore[$paramName] = $paramValue;
	}
	function getParameter($paramName){
		return $this->paramStore[$paramName];
	}
}
?>