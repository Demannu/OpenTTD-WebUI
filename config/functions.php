<?php

require_once("tar.php");

/**
 * Scan a directory for GRF files. $prefix has to be passed as "" on initial function call, it's used in the recursion tree.
 *
 * @return An array of strings with relative paths to all found .grf files from the initial directory
 */
function scanGRFDir($prefix, $dir) {
	global $errors;

	$list = array();
	if (!is_readable($dir)) {
		$errors[] = "<b>$dir is not readable.</b> Please make sure the web server user has read and list access on this directory.";
		return $list;
	}
	$directoryList = opendir($dir);
	while (FALSE !== ($file = readdir($directoryList))) {
		if($file == '.' || $file == '..' 
			|| $file == 'openttdd.grf' || $file == 'openttdw.grf'
			|| $file == 'opengfx' || $file == 'opensfx') continue;
		
		$path = $dir.DIRECTORY_SEPARATOR.$file;
		if (!is_readable($path)) {
			$errors[] = "<b>$path is not readable.</b> Please make sure the web server user has read access (and list, if this is a directory) on this path.";
			return $list;
		}
		if (is_dir($path)) {
			$newPrefix = $prefix.$file.DIRECTORY_SEPARATOR;
			$dirContents = scanGRFDir($newPrefix, $path);
			$list = array_merge($list, $dirContents);
		} else {
			if (substr($path, -4) == '.grf') {
				$list[] = $prefix.$file;
			}
			if (substr($path, -4) == '.tar') {
				$tarContents = scanTar($prefix, $path);
				$list = array_merge($list, $tarContents);
			}			
		}
	}
	return $list;
}

/**
 * Scan a tarball for GRF files. $prefix has to be passed as "" on initial function call, it's used in the recursion tree.
 *
 * @return An array of strings with relative paths to all found .grf files in the tarball
 */
function scanTar($prefix, $tarball) {
	global $errors;
	
	if (!is_readable($tarball)) {
		$errors[] = "<b>$tarball is not readable.</b> Please make sure the web server user has read access  on this tarball.";
		return $list;
	}
	
	$tar = new Archive_Tar($tarball);
	$list = $tar->listContent();
	for ($i = 0; $i < sizeof($list); $i++) {
		$list[$i] = $prefix.$list[$i]['filename'];
	}
	
	$output = array();
	foreach ($list as $item)
		if (substr($item, -4) == '.grf')
			$output[] = $item;
			
	$output = str_replace("/", DIRECTORY_SEPARATOR, $output);
	
	return $output;
}

?>