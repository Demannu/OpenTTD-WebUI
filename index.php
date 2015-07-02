<?php
require 'flight/Flight.php';

// Module Registration

// MySQL Database Connection
// Configure the array parameters for your setup
Flight::register('db', 'PDO', array('mysql:host=localhost;port3306;dbname=openttd', 'USERNAME', 'PASSWORD'), function($db) {
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});

// Mapping Authentication
Flight::map('userAuth', function($user, $pass){
	$dbconn = Flight::db();
	$data = $dbconn->query("SELECT * FROM openttd.users WHERE username='$user' AND password='$pass'");
	$count = $data->rowCount();
	if($count == 1){ setcookie("username", $user, time()+3000); };
});

Flight::map('userCreate', function($user, $pass){
	$dbconn = Flight::db();
	$data = $dbconn->query("INSERT INTO openttd.users (username, password) VALUES ('$user', '$pass')");
});

Flight::map('serverCreate', function($port){
	$username = $_COOKIE["username"];
	$command = "/var/www/public_html/ottd/profiles/generic/ofs-start.py " . $username . " " . $port . " > /dev/null 2>&1 & echo $!;";
	exec($command, $output);
});

// Routing Configuration
Flight::route('/', function(){
	if(!isset($_COOKIE["username"]) || $_COOKIE["username"] == ''){
		Flight::render('login', array(), 'body_content');
	    Flight::render('layout', array('title' => 'OpenTTD WebUI'));
	} else {
		Flight::render('dash', array(), 'body_content');
		Flight::render('layout', array('title' => 'OpenTTD WebUI'));
	};
});

Flight::route('POST /login', function(){
	$username = $_POST["username"];
	$password = $_POST["password"];
	Flight::userAuth($username, $password);
	Flight::redirect('/');
});

Flight::route('POST /register', function(){
	$username = $_POST["username"];
	$password = $_POST["password"];
	Flight::userCreate($username, $password);	
	Flight::userAuth($username, $password);
	Flight::redirect('/');
});

Flight::route('/logout', function(){
	setcookie("username");
	Flight::redirect('/');
});

Flight::route('POST /server/create', function(){
	Flight::serverCreate($_POST['port']);
	Flight::redirect('/');
});

Flight::start();
?>
