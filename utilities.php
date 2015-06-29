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

