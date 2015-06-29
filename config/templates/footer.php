</table><br />
<input type="submit" value="Save configuration" /> <input type="reset" value="Reset options" />
</form>

<h3>Activate NewGRFs</h3>
<select size='1' id='newgrfs' style='width: 600px !important'>
	<?php foreach ($combinedGRFs as $escapedGRF => $grf) { ?>
	<option value='<?php echo $escapedGRF; ?>'><?php echo $grf; ?></option>
	<?php } ?>
</select>
<input type="button" value="Add selected NewGRF" onclick='javascript:addSelectedGRF();' />
</body>
</html>