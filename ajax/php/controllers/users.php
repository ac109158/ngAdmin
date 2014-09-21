<?php
class ControllerUsers {
	public function __construct() {
		session_start();
		$vars = array();// init $vars
	}

	public function register() {
		Utils::log('Lets get You Registred!');
		//Instantiate the User object
		$user = new ptejada\uFlex\User();

		//Add database credentials
		$user->config->database->host     = DB_HOST;
		$user->config->database->user     = DB_USER;
		$user->config->database->password = DB_PASS;
		$user->config->database->name     = DB_NAME;//Database name

		/*
		 * You can update any customizable property of the class before starting the object
		 * construction process
		 */

		//Start object construction
		$user->start();
		$_POST['groupID'] = 0;
		$input            = new ptejada\uFlex\Collection($_POST);
		Utils::log($input, 'This is the $input data');
		$registered = $user->register(array(
				'Username'  => $input->username,
				'Password'  => $input->password,
				'Password2' => $input->password2,
				'Email'     => $input->email,
				'GroupID'   => $input->groupID,
			), true);

		if ($registered) {
			echo "User Registered";
		} else {
			//Display Errors
			foreach ($user->log->getErrors() as $err) {
				echo "<b>Error:</b> {$err} <br/ >";
			}
		}
	}

}

?>
