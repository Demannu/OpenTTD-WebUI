
function removeGRF(trName) {
	document.getElementById(trName).innerHTML = '';
	document.getElementById(trName).style.display = 'none';
}

function addSelectedGRF() {
	// get selected grf name
	index = document.getElementById('newgrfs').selectedIndex;
	grf = document.getElementById('newgrfs').options[index].value;
	
	// check if the required TR exists yet, if not, create it
	trName = "grf_" + grf;
	if (document.getElementById(trName) == null)
		document.getElementById("maintable").innerHTML += '<tr id="' + trName + '"></tr>';
	tr = document.getElementById(trName);
	
	unescapedGRF = grf.replace(/\-\-slash\-\-/g, "/").replace(/\-\-backslash\-\-/g, "\\");;
	
	// add the elements inside the TR
	tr.innerHTML = "<td><b>" + unescapedGRF + "</b> = </td><td><input type='text' name='set_options[" + grf + "]' value='' /></td><td><a href='javascript: removeGRF(\"" + trName + "\");'>Remove</a></td>";
	
	// set TR to be visible
	tr.style.display = '';
}