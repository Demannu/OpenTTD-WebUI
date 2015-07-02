<?php
function create_OTTD($port, $saveFile) {
	$username = $_COOKIE["username"];
	$command = "/var/www/public_html/ottd/profiles/generic/ofs-start.py " . $username . " " . $port"> /dev/null 2>&1 & echo $!;";
	$pid = exec($command, $output);
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
?>
