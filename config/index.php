<?php

require_once("config.php");
require_once("functions.php");

$errors = array();

// Open and parse the known options file
if (!is_readable("options.txt")) {
	$errors[] = "<b>options.txt doesn't exist or is not readable.</b> Please make sure you installed the script correctly";
} else {
	$optionStrings = file("options.txt");
	$options = array();
	foreach ($optionStrings as $optionString) {
		if (trim($optionString) == "") continue;

		$optionArray = explode("\t", $optionString);
		$optionName = trim($optionArray[0]);
		$optionType = trim($optionArray[1]);
		if ($optionType == "t") {
			$optionValues = false;
			$optionDescription = trim($optionArray[2]);
		} else if ($optionType == "b") {
			$optionType = "c";
			$optionValues = array("true", "false");
			$optionDescription = trim($optionArray[2]);
		} else {
			$optionValues = explode(",", trim($optionArray[2]));
			$optionDescription = trim($optionArray[3]);
		}
		$option = array("name" => $optionName,
						"type" => $optionType,
						"values" => $optionValues,
						"desc" => $optionDescription);
		$options[$optionName] = $option;
	}
}

// Scan and save all about the available GRF files...
$allGRFs = array_merge(scanGRFDir("", $config["content_download_data_dir"]), scanGRFDir("", $config["grf_files_dir"]));
$escapedGRFs = str_replace("/", "--slash--", str_replace("\\", "--backslash--", $allGRFs));
$combinedGRFs = array_combine($escapedGRFs, $allGRFs);
	
if (!is_readable($config["filename"]) || !is_writeable($config["filename"])) {
	$errors[] = "<b>openttd.cfg doesn't exist or is not readable or writable.</b> Please make sure you configured the script correctly and web server user has read and write access on the file.";
} else {
	// If data is posted, save to openttd.cfg
	if (isset($_POST["set_options"])) {
		$data = file($config["filename"]);
		$handledPostData = array();
		// Options
		$newGRFHeader = -1;
		for($i = 0; $i < sizeof($data); $i++) {
			$line = trim($data[$i]);
			if ($line == "") continue;
			
			if ($line == "[newgrf]") {
				$lastInNewGRFLine = $i;
				$inNewGRFBlock = true;
			} else if ($line[0] == "[")
				$inNewGRFBlock = false;
			
			if ($line[0] == "[") continue;
			
			if ($inNewGRFBlock)
				$lastInNewGRFLine = $i;
			
			$temp = explode("=", $line);
			$optionName = trim($temp[0]);
			$optionValue = trim($temp[1]);
			
			$escapedOptionName = str_replace("/", "--slash--", str_replace("\\", "--backslash--", $optionName));
				
			if (!isset($options[$optionName]) && substr($optionName, -4) != '.grf') continue;
			
			if (isset($_POST["set_options"][$optionName])) {
				$newValue = $_POST["set_options"][$optionName];
				$data[$i] = $optionName." = ".$newValue."\n";
				$handledPostData[$optionName] = true;
			} else if (isset($_POST["set_options"][$escapedOptionName])) {
				$newValue = $_POST["set_options"][$escapedOptionName];
				$data[$i] = $optionName." = ".$newValue."\n";
				$handledPostData[$optionName] = true;
			} else if (substr($optionName, -4) == '.grf' && !isset($_POST["set_options"][$escapedOptionName])) {
				$data[$i] = ''; // remove GRF that was removed from form by JS
			}
		}

		// add GRFs
		foreach ($_POST["set_options"] as $key => $value) {
			// $key will be escaped, check if we know it and skip if it isnt a GRF definition
			if (!array_key_exists($key, $combinedGRFs)) continue;
			
			// get the data to insert
			$grfName = $combinedGRFs[$key]; // unescaped GRF name
			if ($handledPostData[$grfName] == true) continue;
			$line = $grfName." = ".$value."\n";
			
			// shift all data after the $lastInNewGRFLine one down
			for ($i = sizeof($data)-1; $i > $lastInNewGRFLine ; $i--) {
				$data[$i+1] = $data[$i];
			}
			
			// insert the new NewGRF line just after the NewGRF header
			$data[$lastInNewGRFLine  + 1] = $line;
		}
		
		file_put_contents($config["filename"], implode("", $data));
	}
}

// Start the output
include("templates/header.php");

if (sizeof($errors) == 0) {
	// Open, parse and display openttd.cfg
	$data = file($config["filename"]);
	foreach($data as $line) {
		$line = trim($line);
		if ($line == "") continue;

		if ($line[0] == "[") {
			// Header
			$header = $line;
			$lastHeader = $header;
			if ($header == "[newgrf]") {
				include("templates/options_header.php");
				$header = false;
			}
			continue;
		}
		
		$temp = explode("=", $line);
		$optionName = trim($temp[0]);
		$optionValue = trim($temp[1]);
		
		if (!isset($options[$optionName]) && $lastHeader != "[newgrf]") continue;
		
		if ($header != false) {
			include("templates/options_header.php");
			$header = false;
		}
		
		if ($lastHeader != "[newgrf]") {
			// Normal options.	
			$option = $options[$optionName];
			include("templates/option_$option[type].php");		
		} else {
			// Active NewGRFs
			$grfFile = str_replace("/", "--slash--", str_replace("\\", "--backslash--", $optionName));
			$grfParams = $optionValue;
			
			// TODO: export this to a template accessible both from PHP and JS (for adding NewGRFs)
			echo "<tr id = 'grf_$grfFile'><td><b>$optionName</b> = </td>
				<td><input type='text' name='set_options[$grfFile]' value='$grfParams' /></td>
				<td><a href='javascript: removeGRF(\"grf_$grfFile\");'>Remove</a></td></tr>";
		}	
	}
}

include("templates/footer.php");
?>