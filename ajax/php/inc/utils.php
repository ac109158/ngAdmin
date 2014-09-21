<?php
class Utils {

	function __construct() {
		// Base Model Class
		$this->sitename    = SITENAME;
		$this->rand_key    = RAND_KEY;
		$this->admin_email = 'ac109158@plusonecompany.info';
	}

	public function index($vars) {
		$this->log('NEW CYCLE', 'debug.html');
		if (!$this->execute($vars['controller'], $vars['$task'])) {
			$this->execute('landings', 'display');// (controller, task)
			exit;
		}
	}

	// request($param) test if a value exists and removes htmlenitites;
	// return null is value if not set.
	public function request($param) {
		if (isset($param)) {
			$request = $param;
			if (is_string($request)) {
				$request = trim(htmlentities($request));
			}
			return $request;
		}
		return null;
	}

	public function CollectFormSubmission() {
		$array    = $this->cleanArray($_POST);
		$formvars = array();
		foreach ($array as $key => $value) {
			if (!empty($key) && !empty($value)) {
				$formvars[$key] = $this->SafeDisplay($value);
			}
		}

		return $formvars;
	}

	// VIEW TOOLS

	//injectVarsIntoObject($obj, $vars) will take $vars as set each as a $this of the object
	public function injectVarsIntoObject($obj, $vars) {
		foreach ($vars as $key => $value) {
			if (!empty($value) && !empty($key)) {
				$this->log('KEY: '.$key.'VALUE: '.$value, 'debug.html');
				$obj->$key = $this->$value;
			}

		}
		return $obj;
	}

	public function injectSessionIntoThis() {
		foreach ($_SESSION as $key => $value) {
			$this->$key = $value;
		}

	}

	//fetchModel($model, $action=false, $arg = false)
	// verifys the path and imports the model
	// executes the $task if passed as an argument, $taskArg is optional argument for $task
	public function fetchModel($model, $task = false, $taskArg = false) {
		$this->log('Utils->fetchModel->'.$model.'->'.$task.'->'.$taskArg, 'debug.html');
		$path       = MODEL.$model.'.php';
		$class_name = 'Model'.ucfirst($model);
		if (!file_exists("$path")) {return false;}
		require_once "$path";
		if ($task) {
			if (!method_exists("$class_name", "$task")) {return false;}
			$model = new $class_name();
			return $model->$task($value = ($taskArg != false)?$taskArg:void);
		}
		$this->log("<<< ".$model.'->'.$task.'->'.$taskArg, 'debug.html');
		return new $class_name();
	}

	//fetchView($view, $vars = false)
	// used for retriving View Classes,
	public function fetchView($view, $vars = false) {
		$this->log('Utils->fetchView->'.$view, 'debug.html');
		$viewpath = VIEW.$view.'/view.html.php';
		$this->log('$viewpath : '.$viewpath, 'debug.html');
		if (!file_exists("$viewpath")) {
			$this->log($viewpath.'does not exist', 'debug.html');
			die('View does not Exist');
		}
		require_once ($viewpath);
		$view_name = 'View'.ucfirst($view);
		if (class_exists($view_name)) {
			$viewclass = new $view_name();
			if (isset($vars) && is_array($vars))// If the $vars was passed in the variable will be injected in to the view object
			{
				$this->log("$view_name was injected with vars", 'debug.html');
				$this->log($vars, 'debug.html');

				$viewclass = $this->injectVarsIntoObject($viewclass, $vars);
			}
			$this->log("<<< ".ucfirst($view).' View Class', 'debug.html');
			return $viewclass;
		} else {
			$this->log($view_name.'does not exist', 'debug.html');
			die('View class does not exists');
		}

	}

	public function fetchOptionView($vars) {
		////////////////////////////////////////////////////////////
		$this->log('Utils->fetchOptionView->'.$vars['option'], 'debug.html');
		$optionpath = VIEW.$vars['view'].'/'.$vars['option'].'/'.'view.html.php';
		if (!file_exists("$optionpath")) {$this->log("(FAIL) $optionpath", 'debug.html');die('View does not Exist');}
		require_once ($optionpath);
		$option_name = 'View'.ucfirst($vars['option']);
		if (class_exists($option_name)) {
			$this->log("<<< ".ucfirst($vars['option']).' View Class', 'debug.html');
			return new $option_name();
		} else {
			$this->log("(FAIL)<<< ".ucfirst($vars['option']).' View Class does not exist', 'debug.html');
			die('View class does not exists');
		}

	}

	public function requireHeader($header, $vars = false) {
		$headerpath = HEADER.$header.'/view.html.php';
		if (!file_exists("$headerpath")) {die('Header does not Exist');}
		require_once ($headerpath);

	}

	public function requireFooter($footer, $vars = false) {
		$footerpath = FOOTER.$footer.'/view.html.php';
		if (!file_exists("$footerpath")) {die('Footer does not Exist');}
		require_once ($footerpath);

	}

	public function fetchController($controller, $task) {
		$path = CONTROLLER.$controller.'.php';
		Utils::log($path, 'The path of the controller.');
		$class_name = 'Controller'.ucfirst($controller);
		if (file_exists("$path")) {require_once "$path";}
		if (method_exists("$class_name", "$task")) {return new $class_name();}
		return false;
	}

	public function execute($controller, $task, $arg = false) {
		Utils::log('Utils->execute');
		if (!$controller = Utils::fetchController($controller, $task)) {return false;}
		if (!$controller->$task($value = ($arg != false)?$arg:null)) {return false;}
		return true;
	}

	public function redirect($vars) {
		$task = $vars['task'];
		if (!$controller = Utils::fetchController($vars['controller'], $vars['task'])) {return false;}
		if (!$controller->$task($vars)) {return false;}
		exit;
	}

	// parses the url into an array
	public function urlToArray() {
		$url               = array();
		$url['controller'] = $this->request($_REQUEST['controller']);
		$url['task']       = $this->request($_REQUEST['task']);
		$url['view']       = $this->request($_REQUEST['view']);
		$url['action']     = $this->request($_REQUEST['action']);
		$url['option']     = $this->request($_REQUEST['option']);
		$url['filter']     = $this->request($_REQUEST['filter']);
		$url['filter2']    = $this->request($_REQUEST['filter2']);
		return $url;
	}

	// cleanArray($array) takes an returns an array without htmlentitties
	public function cleanArray($array) {
		$clean_array = array();
		foreach ($array as $name => $value) {
			$clean_array["$name"] = $this->SafeDisplay("$value");
		}
		return $clean_array;
	}

	public function log($data, $input = null, $file = 'debug.html', $method = 'a', $path = LOGS) {
		$outBuffer   = '';
		$filepath    = (isset($path))?$path.$file:'logs/'.$file;
		$file_handle = fopen("$filepath", $method) or die('Cannot open file:  '.$filepath);
		$outBuffer .= '<hr>';
		if ($input && is_string($input)) {
			$outBuffer .= ' '.$input.'<br><br>';
		}
		if (is_array($data)) {
			$outBuffer .= '*'.$data.'*'.'<br>';
			foreach ($data as $key => $value) {
				$outBuffer .= $key.':'.$value.'<br>';
			}
			$outBuffer .= '**'.$data.'**'.'<br><br>';
		} else if (is_object($data)) {
			$outBuffer .= "<pre>";
			$outBuffer .= serialize($data);
			$outBuffer .= "</pre>";
		} else {
			$outBuffer .= $data.'<br><br>';
		}
		$outBuffer .= '<hr>';

		fwrite($file_handle, $outBuffer);
		fclose($file_handle);
	}

	public function SafeDisplay($value) {
		if (empty($value) || is_array($value)) {
			return '';
		}
		return htmlentities($value);
	}

	public function getDBC($host = DB_HOST, $user = DB_USER, $pass = DB_PASS, $name = DB_NAME) {

		$this->log('getDBC('.$host.','.$user.','.'**********'.','.$name.')', 'debug.html');
		$mysqli = new mysqli($host, $user, $pass, $name);
		if ($mysqli->connect_error) {
			die('Connect Error ('.$mysqli->connect_errno.') '.$mysqli->connect_error);
		}
		$this->log(' getDBC() << Success... '.$mysqli->host_info, 'debug.html');
		return $mysqli;
	}

	// getPusherSocket()  will be used for PUSHER functions
	public function getPusherSocket() {
		require_once INC.'Pusher.php';
		$pusher = new Pusher(APP_KEY, APP_SECRET, APP_ID);
		return $pusher;
	}

	public function getUrl() {
		$url = @($_SERVER["HTTPS"] != 'on')?'http://'.$_SERVER["SERVER_NAME"]:'https://'.$_SERVER["SERVER_NAME"];
		$url .= ($_SERVER["SERVER_PORT"] !== 80)?":".$_SERVER["SERVER_PORT"]:"";
		$url .= $_SERVER["REQUEST_URI"];
		return $url;
	}

	// form collection

	// isArrayFull($array) takes all of the values of an array and check if they are all set
	// Use on POST, GET to check if all of the fields of a form were filled out - basic validation
	// returns a message to fill in the first empty field it comes to / ignores submit
	public function isArrayFull($array) {
		foreach ($array as $value => $contents) {
			if (!$contents && $value != 'submit') {
				if (!$pos = strpos($value, '_')) {
					return 'Please fill in '.$value;
				}
				$value = str_replace('_', ' ', $value);
				return 'Please fill in '.$value;
			}
		}
		return true;
	}

	// CollectFormSubmission($global= '$_POST') will take all of the values of a super global and pass them into an array
	// removes the html entities

	//validation functions

	// numbersOnly($string) extracts only numbers in a string
	// used for collecting phone number
	public function numbersOnly($string) {
		return preg_replace('/\D/', '', $string);
	}

	// basic phone validation that counts for 7 or 10 or 12 digits in a sring
	// Boolen.. does not modify string
	public function validatePhone($string) {
		$numbersOnly    = preg_replace('/\D/', '', $string);
		$numberOfDigits = strlen($numbersOnly);
		if ($numberOfDigits == 7 || $numberOfDigits == 10 || $numberOfDigits == 11) {
			return true;
		} else {
			return false;
		}
	}

	//isMatch($var1, $var2) basic compare function
	//
	public function isMatch($var1, $var2) {
		return $var1 === $var2;
	}

	//Sanitization functions

	public function Sanitize($str, $remove_nl = true) {
		$str = $this->StripSlashes($str);
		if ($remove_nl) {
			$injections = array('/(\n+)/i',
				'/(\r+)/i',
				'/(\t+)/i',
				'/(%0A+)/i',
				'/(%0D+)/i',
				'/(%08+)/i',
				'/(%09+)/i',
			);
			$str = preg_replace($injections, '', $str);
		}
		return $str;
	}

	function StripSlashes($str) {
		if (get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return $str;
	}

	function GetSpamTrapInputName() {
		return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
	}

	public function GetLoginSessionVar() {
		$retvar = md5($this->$rand_key);
		$retvar = 'usr_'.substr($retvar, 0, 10);
		return $retvar;
	}

	function SanitizeForSQL($str) {
		$ret_str = addslashes($str);
		return $ret_str;
	}

}
?>
