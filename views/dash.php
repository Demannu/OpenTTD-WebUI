
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">
        <img alt="Brand" src="...">
      </a>
      <p class="navbar-text">Signed in as <?php echo $_COOKIE["username"]; ?></p>
    </div>
    <button type="button" class="btn btn-default navbar-btn "><a href="/logout">Logout</a></button>
  </div>
</nav>


<h2>Create a Server</h2>
	<form action="/server/create" method="post">
		Port: 
		<input type="text" name="port" maxlength="5">
		<input type="submit">
	</form>
<h2>View User Saves</h2><br>
<?php
		$user = $_COOKIE["username"];
		$fileDir = '/var/www/public_html/ottd/profiles/' . $user;
		if ($handle = opendir($fileDir)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && strpos($entry, '.sav') !==false) {
					echo "$entry <br>";
				}
			}
		closedir($handle);
		};
?>
<h2>Destroy a Server</h2>
<form ation="index.php" method="post">
Port: 
<input type="text" name="pid"><br>
<input type="hidden" name="method" value="destroy">
<input type="submit">
<h2>Running Servers</h2>
<?php
	exec("ps ax|grep openttd|grep -v grep", $output);
	foreach ($output as $entry) {
		echo $entry;
	};
?>