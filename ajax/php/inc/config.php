<?php

//setup helper functions

define('SITENAME', 'MEDIA');
define('SLOGAN', '...Dynamic Web Development');
define('BASE_URL', $_SERVER['DOCUMENT_ROOT'].'/ngAdmin');
define('UFLEX', BASE_URL.'/ajax/php/inc/uFlex/');
define('LOGS', BASE_URL.'/logs/');
define('URL', 'http://andy.plusonedevelopment.com/ngAdmin');
define('CONTROLLER', BASE_URL.'/ajax/php/controllers/');
define('MODEL', BASE_URL.'/ajax/php/models/');
define('VIEW', BASE_URL.'/ajax/php//views/');
define('HEADER', './components/views/header/');
define('FOOTER', './components/views/footer/');

define('LIB', './lib/');
define('SCRIPT', './scripts/');
define('CSS', './css/');
define('BOOTSTRAP', './css/bootstrap/');
define('JS', './js/');

$date = date("l  F j, o");
define('DATE', "$date");

define('DB_TYPE', 'mysql');
define('DB_HOST', 'mysql.andy.plusonedevelopment.com');
define('DB_NAME', 'andy_database');
define('DB_USER', 'andy2013');
define('DB_PASS', 'PlusOne');

define('DB_USERS_TABLE', 'mh_users');

define('STYLE', CSS.'style.css');

define('RAND_KEY', '8iQc5oik66oVZe6');// DO NOT CHANGE
define('SALT', '8iQc5oik66oVZe6');// DO NOT CHANGE

define('LOGIN_ATTEMPTS', '6');// Number of attempts before lockout
