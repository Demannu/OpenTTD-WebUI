<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">

		<title><?php echo $title; ?></title>
		<link href="/css/bootstrap.css" rel="stylesheet">
		<link href="/css/<?php echo $theme; ?>.css" rel="stylesheet">
    	<!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    	<![endif]-->
	</head>

	<body>
		<?php echo $body_content; ?>

	    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
	    <script src="/js/custom.js"></script>
	    <script src="/js/bootstrap.min.js"></script>
	</body>
</html>