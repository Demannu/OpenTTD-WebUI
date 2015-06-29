<tr>
	<td><?php echo $option["name"]; ?></td>
	<td>
		<select size='1' name='set_options[<?php echo $option["name"]; ?>]'>
			<?php foreach ($option["values"] as $value) { ?>
			<option value='<?php echo $value; ?>'<?php echo($optionValue == $value ? 'selected' : ''); ?>><?php echo $value; ?></option>
			<?php } ?>
		</select>
	</td>
	<td><?php echo $option["desc"]; ?></td>
</tr>
