<?php
function create_OTTD($port=3979, $saveFile, $config) {
	$command = "/usr/games/openttd -D -f -n 0.0.0.0:" . $port . " -g '". $saveFile . "' -c '" . $config . "'" . " > /dev/null 2>&1 & echo $!;";
	$pid = exec($command, $output);
	echo($pid + 1);
};
function destroy_OTTD($pid) {
	exec("kill -9 $pid");
	echo 'Killed: ' . $pid;
};
if(isset($_POST["user"]) & $_POST["port"] > 0) {
	$port = $_POST["port"];
	$saveFile = '/var/www/.openttd/save/' . $_POST["user"] . '/' . $_POST["filename"] .'';
	$config = '/var/www/.openttd/save/' . $_POST["user"] . '/openttd.cfg';
//	create_OTTD($port,$saveFile,$config);
} elseif(isset($_POST["pid"])) {
//	destroy_OTTD($_POST["pid"]);
} else {
	if($_POST["port"] < 0){ echo("Negative Port!"); };
	$user = $_POST["user"];
	$fileDir = '/var/www/.openttd/save/' . $user;
	echo '<html><body><h2>Create a Server</h2><form action="spawn.php" method="post">Username: <input type="text" name="user"><br>Filename (use final_version.sav) <input type="text" name="filename"><br>Port: <input type="text" name="port" maxlength="5"><input type="submit"></form>';
	echo '<h2>View User Saves</h2><br>';
	if ($handle = opendir($fileDir)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != ".." && strpos($entry, '.sav') !==false) {
				echo "$entry <br>";
			}
		}
	closedir($handle);
	};
	echo '<h2>Destroy a Server</h2><form ation="spawn.php" method="post">Port: <input type="text" name="pid"><br><input type="submit"></body></html>';
	echo '<h2>Running Servers</h2>';
	exec("ps ax|grep openttd|grep -v grep", $output);
	foreach ($output as $entry) {
		echo $entry;
	};
};
?>
