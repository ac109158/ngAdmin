<?php
session_start();
$_SESSION['write']    = (isset($_SESSION['write']))?$_SESSION['write']:'w';
$_SESSION['status']   = (isset($_SESSION['status']))?$_SESSION['status']:0;
$_SESSION['attempts'] = (isset($_SESSION['attempts']))?$_SESSION['attempts']:6;
if ($_COOKIE) {
	setcookie('last_active', $_COOKIE['sign_in']);

} else {
	pass;
}

ini_set('display_errors', 'On');
error_reporting(E_ALL^E_NOTICE);

/**
 *Written by Andy Cook8/21/13
 *This program is to build out the projects for CS-4000 PHP Class    *
 */
/* Get the controller from the url
 *******************************************************/

function main() {

	require_once './inc/config.php';
	require_once './inc/utils.php';
	require_once (UFLEX.'autoload.php');
	$utils = new Utils();
	$utils->log($_SESSION, '$_SESSION', 'debug.html', 'w');
	$utils->log($_SESSION[userData], '$_SESSION[userData');
	// $utils->log('START', 'debug.html');
	$utils->log('$_SESSION["attempts"] = '.$_SESSION['attempts']);
	if ($_SESSION['redirect'] == true) {
		require_once 'inc/redirect.php';
		$utils->log('**redirect control***', 'debug.html');
		$utils->execute($vars['controller'], $vars['task'], $vars);// (controller, task)
		exit;

	} else {
		$url = $utils->urlToArray();
		$utils->log($url, 'This should be the $url');
		if (!isset($url['controller']) || !isset($url['task'])) {
			$utils->log('Controller or task not set', 'debug.html');
			$_REQUEST['view'] = 'landing';
			$utils->execute('landings', 'display');// (controller, task)
			exit;
		}
		$utils->log('**Execute '.$url['controller'].'->'.$url['task'].' ***');
		$result = $utils->execute($url['controller'], $url['task']);
		$utils->log($result, 'Execute success?');
		exit;

	}

}

/**********************************************************
 **
 */
main();
?>
