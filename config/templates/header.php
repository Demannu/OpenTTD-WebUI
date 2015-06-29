<html>
<head>
	<title>OpenTTD Server Configuration Tool</title>
	<link rel="stylesheet" type="text/css" href="style.css" />
	<script type="text/javascript" src="newgrf.js"></script>
</head>
<body>
<?php foreach ($errors as $error) { ?>
<p class="error"><?php echo $error; ?></p>
<?php } ?>
<h1>openttd.cfg</h1>
<form action="index.php" method="post">
<table id='maintable'>
