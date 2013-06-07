$(document).ready(function() {
	
	/*** Autocomplete for selecting destination ***/
	$("#bus_no").focus(function() {
		$(this).autocomplete('../includes/busAutocomplete.php?op=bus_no&opt=1').result(function(event, data, formatted) {
			data = data.toString();
		});
		return false;
	});
	
});