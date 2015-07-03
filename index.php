<?php
require 'flight/Flight.php';
require 'config.php';

$ottdProfiles = '/var/www/public_html/ottd/profiles/';
$ottdGeneric = '/var/www/public_html/ottd/profiles/generic/';
// Module Registration

// MySQL Database Connection
// Configuration @ config.php
Flight::register('db', 'PDO', array("mysql:host=$dbhost;port3306;dbname=$dbname", $dbuser, $dbpass), function($db) {
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});

// Mapping Authentication
Flight::map('userAuth', function($user, $pass){
	$dbconn = Flight::db();
	$stmt = $dbconn->prepare("SELECT * FROM openttd.users WHERE username = :user_name AND password = :pass_word");
	$data = array( 'user_name' => $user, 'pass_word' => $pass);
	$result = $stmt->execute($data);
	$count = $stmt->rowCount();
	if($count == 1){ setcookie("username", $user, time()+3000); };
});

Flight::map('userCreate', function($user, $pass){
	$dbconn = Flight::db();
	$stmt = $dbconn->prepare("INSERT INTO openttd.users (username, password) VALUES (:user_name, :pass_word)");
	$data = array( 'user_name' => $user, 'pass_word' => $pass);
	if($stmt->execute($data)){
		Flight::userCreateDir($user);
		
		return '1';
	}
});

Flight::map('userCreateDir', function($user){
	$ottdProfiles = '/var/www/public_html/ottd/profiles/';
	$ottdGeneric = '/var/www/public_html/ottd/profiles/generic/';
	$genDir = $ottdGeneric . '*';
	$userDir = $ottdProfiles . $user;
	mkdir($userDir);
	$command = 'cp -R ' . $genDir . ' ' . $userDir;
	exec($command);
});

Flight::map('serverCreate', function($saveGame){
	$username = $_COOKIE["username"];
	$save = "save/" . $saveGame;
	$command = "python /var/www/public_html/ottd/profiles/Demannu/ofs-start.py save/final_version.sav";
	exec($command, $output);
});


// Routing Configuration
Flight::route('/', function(){
	if(!isset($_COOKIE["username"]) || $_COOKIE["username"] == ''){
		Flight::render('login', array(), 'body_content');
	    Flight::render('layout', array('title' => 'OpenTTD WebUI'));
	} else {
		Flight::redirect('/user/' . $_COOKIE["username"]);
	};
});

Flight::route('POST /login', function(){
	$username = $_POST["username"];
	$password = $_POST["password"];
	Flight::userAuth($username, $password);
	Flight::redirect('/user/' . $username);
});

Flight::route('POST /register', function(){
	$username = $_POST["username"];
	$password = $_POST["password"];
	if(Flight::userCreate($username, $password) == 1){
		Flight::userAuth($username, $password);
		Flight::redirect('/user/' . $username);
	} else {
		echo "Somethings fucky with the register boss";
	};

	Flight::redirect('/');
});

Flight::route('/logout', function(){
	setcookie("username");
	Flight::redirect('/');
});

Flight::route('/user/@user', function($user){
	Flight::render('dash', array(), 'body_content');
	Flight::render('layout', array('title' => 'OpenTTD WebUI'));
});

Flight::route('/user/@user/start', function($user){
	Flight::serverCreate($_POST['saveGame']);
	Flight::redirect('/user/' . $user);
});

Flight::route('/test/pdo/@user', function($user){
	$dbconn = Flight::db();
	$stmt = $dbconn->prepare("SELECT serverport FROM openttd.servers WHERE username = :user_name");
	$data = array( 'user_name' => $user);
	$stmt->execute($data);
	$result = $stmt->fetchAll();
	echo $result[0]["serverport"];
});

Flight::start();
?>
