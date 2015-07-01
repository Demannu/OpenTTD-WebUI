<?php
function create_OTTD($port, $saveFile, $config) {
	$command = "/usr/games/openttd -D -f -n 0.0.0.0:" . $port . " -g '". $saveFile . "' -c '" . $config . "'" . " > /dev/null 2>&1 & echo $!;";
	$pid = exec($command, $output);
	$pidC = $pid + 1;
	$username = $_COOKIE["username"];
	$query = "INSERT INTO servers VALUES (DEFAULT, '" . $username . "', '" . $pidC . "', '" . $config . "', '1')";
	if(mysqli_query($dbconn, $query)) {
		header('Location: index.php');
	} else {
		echo "<div class='dbError'>
		<span class='systemWarning'>" .mysqli_error($dbconn) ." </span>
		</div> ";
	};
};
function destroy_OTTD($pid) {
	exec("kill -9 $pid");
	$query = "UPDATE * FROM servers SET 'status'='0' WHERE 'pidinfo'=" . $pid . "'";
	if(mysqli_query($dbconn, $query)) {
		header('Location: index.php');
	} else {
		echo "<div class='dbError'>
		<span class='systemWarning'>" .mysqli_error($dbconn) ." </span>
		</div> ";
	};
};

function create_User($username){
	$genDir = '/var/www/public_html/ottd/profiles/generic/*';
	$userDir = '/var/www/public_html/ottd/profiles/' . $username;
	mkdir($userDir);
	$command = 'cp -R ' . $genDir . ' ' . $userDir;
	exec($command);
};

