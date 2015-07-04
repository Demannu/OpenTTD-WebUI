
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <p class="navbar-text">Signed in as <?php echo $_COOKIE["username"]; ?></p>
    </div>
    <button type="button" class="btn btn-default navbar-btn "><a href="/config">Configure</a></button>
    <button type="button" class="btn btn-default navbar-btn "><a href="/logout">Logout</a></button>
  </div>
</nav>
<div class="container-fluid">
	<div class="row col-md-3 col-md-offset-2">
		<div class="list-group">
			<center>
			<h3> OpenTTD Save Selector </h3>
			<?php
				$fileDir = '/var/www/public_html/ottd/profiles/' . $_COOKIE["username"] . "/save/";
				if ($handle = opendir($fileDir)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != ".." && strpos($entry, '.sav') !==false) {
							$location = "/user/" . $_COOKIE['username'] . "/download/" . $entry;
							echo "<button type='button' class='list-group-item'><a href='$location'>Download</a> | <a class='saveLink' name='$entry'>$entry</a></button>";
						}
					}
				closedir($handle);
				};
			?>
			<br>
			<form class="form-inline" action='/user/<?php echo $_COOKIE["username"]; ?>/start' method='post'>
				<div class="form-group">
					<label for="save">Game Save </label>
					<input type="text" id="save" name="saveGame">
					<button type="submit" class="btn btn-default">Launch Server</button>
				</div>
			</form>
			</center>
		</div>
	</div>
	<div class="row col-md-3 col-md-offset-1">
		<div class="list-group">
			<center>
			<h3> OpenTTD Running Servers </h3>
			<?php
				exec("ps ax -o pid,comm|grep openttd|grep -v grep", $output);
				foreach ($output as $entry) {
					$boom = explode(" ", $entry);
					$pid = $boom[0];
					$username = $_COOKIE['username'];
					echo "<form class='form-inline' action='/user/$username/destroy' method='post'><div class='form-group'><label for='pid'>$pid - OpenTTD Server   </label><input type='hidden' name='pid' value='$pid'><button type='submit' class='btn btn-default'>Kill Server</button></div></form>";
				};
			?>
			<br>
			</center>
		</div>
	</div>
</div>

