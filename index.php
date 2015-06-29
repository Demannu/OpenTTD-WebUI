<?php
$dbhost = 'localhost';
$dbuser = 'USERNAME';
$dbpass = 'PASSWORD';
$dbname = 'openttd';

$dbconn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$dbconn){
	die("Connection failed: " . mysqli_connect_error());
}

$method = $_POST["method"];
if($method == 'register'){
	$username = $_POST["username"];
	$password = $_POST["password"];
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
?>

<html>
<head>
<title>Zvarpensg OpenTTD Server Registry </title>
</head>
<body>
<?php if(!isset($_COOKIE["username"])){
	echo "<div class='registration'>
	<center>
	<h2>User Registration</h2>
	<form action='index.php' method='post'>
	Username: <br>
	<input class='username' type='text' name='username'> <br>
	Password: <br>
	<input class='password' type='password' name='password'><br>
	<input type='hidden' name='method' value='register'>
	<input type='submit' value='Register'> </center>
	</div> "; };
?>

<?php if(isset($_COOKIE["username"])){
	echo "<h2>Welcome " . $_COOKIE['username'] . "!</h2>";
};
?>
</body>
</html>
