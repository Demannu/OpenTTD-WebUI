<?php
if ($_GET["logout"] == 'true')
{
	setcookie('username');
	header('Location: index.php');
}
require 'utilities.php';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '@lph@m@le';
$dbname = 'openttd';

$dbconn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$dbconn){
	die("Connection failed: " . mysqli_connect_error());
}

if($_POST["method"] == 'register'){
	$username = mysqli_real_escape_string($dbconn,$_POST["username"]);
	echo('sawp nigs');
	$password = mysqli_real_escape_string($dbconn,$_POST["password"]);
	$query = "INSERT INTO users VALUES (DEFAULT, '" . $username . "', '" . $password . "')";
	if(mysqli_query($dbconn, $query)) {
		setcookie("username", $username, time()+3000);
		header('Location: index.php');
	} else {
		unset($_COOKIE["username"]);
		echo "<div class='dbError'>
		<span class='systemWarning'>" .mysqli_error($dbconn) ." </span>
		</div> ";
	};
};

if($_POST["method"] == 'login'){
	$username = mysqli_real_escape_string($dbconn,$_POST["username"]);
	$password = mysqli_real_escape_string($dbconn,$_POST["password"]);
	$query = "SELECT * FROM users WHERE username='" . $username . "' AND password='" . $password . "'";
	$userQuery = mysqli_query($dbconn, $query);
	$userVerify = mysqli_num_rows($userQuery);
	if($userVerify > 0) {
		setcookie("username", $username, time()+3000);
		header('Location: index.php');
	} else {
		echo "<div class='dbError'>
		<span class='systemWarning'>" .mysqli_error($dbconn) ." </span>
		</div> ";
	};
};

if($_POST["method"] == 'create'){
	if($_POST["port"] > 0 & !isset($_POST["pid"])) {
		$port = $_POST["port"];
		$saveFile = '/var/www/.openttd/save/' . $_COOKIE["username"] . '/' . $_POST["filename"] .'';
		$config = '/var/www/.openttd/save/' . $_COOKIE["username"] . '/openttd.cfg';
	//	create_OTTD($port,$saveFile,$config);
	};
};

if($_POST["method"] == 'destroy'){
	//destroy_OTTD($_POST["pid"]);
}
?>

<html>
<head>
<title>Zvarpensg OpenTTD Server Registry </title>
</head>
<body>
<?php 

if(!isset($_COOKIE["username"]) || $_COOKIE["username"] == ''){
	echo "<div class='login'>
	<center>
	<h2>User Login</h2>
	<form action='index.php' method='post'>
	Username: <br>
	<input class='username' type='text' name='username'> <br>
	Password: <br>
	<input class='password' type='password' name='password'><br>
	<input type='hidden' name='method' value='login'>
	<input type='submit' value='Login'> 
	</form></center>
	</div> ";
	echo "<div class='registration'>
	<center>
	<h2>User Registration</h2>
	<form action='index.php' method='post'>
	Username: <br>
	<input class='username' type='text' name='username'> <br>
	Password: <br>
	<input class='password' type='password' name='password'><br>
	<input type='hidden' name='method' value='register'>
	<input type='submit' value='Register'> 
	</form></center>
	</div> "; };
?>
<?php
if($_COOKIE["username"] != NULL){
	if($_POST["port"] < 0 & $_POST["method"] == 'create'){ echo("Negative Port!"); };
		echo '<html><body><span class="logout"><a href="index.php?logout=true">Logout</a><h2>Create a Server</h2><form action="index.php" method="post">Filename (use final_version.sav) <input type="text" name="filename"><br>Port: <input type="text" name="port" maxlength="5"><input type="hidden" name="method" value="create"><input type="submit"></form>';
		echo '<h2>View User Saves</h2><br>';
		$user = $_COOKIE["username"];
		$fileDir = '/var/www/.openttd/save/' . $user;
		if ($handle = opendir($fileDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, '.sav') !==false) {
					echo "$entry <br>";
				}
			}
		closedir($handle);
		};
	echo '<h2>Destroy a Server</h2><form ation="index.php" method="post">Port: <input type="text" name="pid"><br><input type="hidden" name="method" value="destroy"><input type="submit"></body></html>';
	echo '<h2>Running Servers</h2>';
	exec("ps ax|grep openttd|grep -v grep", $output);
	foreach ($output as $entry) {
		echo $entry;
	};
};
?>
</body>
</html>
