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

// User Functions
Flight::map('userAuth', function($user, $pass){
	$dbconn = Flight::db();
	$stmt = $dbconn->prepare("SELECT password FROM openttd.users WHERE username = :user_name");
	$data = array( 'user_name' => $user);
	$stmt->execute($data);
	$results = $stmt->fetchAll();
	$salt = $results[0]['password'];
	$pass = password_verify($pass, $salt);
	if($pass == true){ setcookie("username", $user); };
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

// Server Functions
Flight::map('serverCreate', function($saveGame){
	$username = $_COOKIE["username"];
	if($saveGame == ""){
		$save = " ";
	} else {
			$save = "save/" . $saveGame;
	};
	$command = "python /var/www/public_html/ottd/profiles/'$username'/ofs-start.py '$save' '$username'";
	exec($command);
});

Flight::map('serverDestroy', function($pid){
	$command = "/bin/kill -9 '$pid'";
	exec($command, $output);
	return $output;
});

Flight::map('downloadSave', function($user, $file){
	Flight::redirect("/profiles/$user/save/$file");
});

// Routing Configuration
Flight::route('/', function(){
	if(!isset($_COOKIE["username"]) || $_COOKIE["username"] == ''){
		Flight::render('login', array(), 'body_content');
	    Flight::render('layout', array('title' => 'OpenTTD WebUI', 'theme' => 'login'));
	} else {
		Flight::redirect('/user/' . $_COOKIE["username"]);
	};
});

Flight::route('POST /login', function(){
	$username = $_POST["username"];
	$password =$_POST["password"];
	Flight::userAuth($username, $password);
	Flight::redirect('/user/' . $username);
});

Flight::route('POST /register', function(){
	$username = $_POST["username"];
	$options = ['salt' => 'putZeLimeindaCocounutz'];
	$password = password_hash($_POST["password"], PASSWORD_BCRYPT, $options);
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
	if($user == $_COOKIE['username']){
		Flight::render('dash', array(), 'body_content');
		Flight::render('layout', array('title' => 'OpenTTD WebUI', 'theme' => 'dash'));
	} else {
		Flight::redirect($_COOKIE['username']);
	};
});

Flight::route('/user/@user/start', function($user){
	Flight::serverCreate($_POST['saveGame']);
	Flight::redirect('/user/' . $user);
});

Flight::route('/user/@user/destroy', function($user){
	Flight::serverDestroy($_POST['pid']);
	Flight::redirect('/user/' . $user);
});

Flight::route('/user/@user/download/@file', function($user, $file){
	Flight::downloadSave($user, $file);
	Flight::render('download', array(), 'body_content');
	Flight::render('layout', array('title' => 'GameSave Download', 'theme' => 'dash'));
});

Flight::route('/user/@user/failed', function($user){
	Flight::render('dash', array(), 'body_content');
	Flight::render('layout', array('title' => 'OpenTTD WebUI', 'theme' => 'dash'));
});

Flight::route('/test/pdo/@user', function($user){
	$dbconn = Flight::db();
	$stmt = $dbconn->prepare("SELECT serverport FROM openttd.servers WHERE username = :user_name");
	$data = array( 'user_name' => $user);
	$stmt->execute($data);
	$result = $stmt->fetchAll();
});

Flight::route('/test/ps', function(){
	exec("ps -o pid,comm", $output);
	var_dump($output);
	foreach ($output as $entry) {
		echo "<button type='button' class='list-group-item'><a href=>Kill</a> | $entry</button>";
	};
});

// Take off
Flight::start();
?>
