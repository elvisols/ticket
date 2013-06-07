$(document).ready(function() {
	$("#origin, #destination").focus(function() {
		$(this).autocomplete('includes/autocomplete.php?op=destination&opt=1').result(function(event, data, formatted) {
			data = data.toString();
		});
		return false;
	});
	
	$("#origin").keypress(function(e) {
		if (e.which === 13) {
			return false;
		}
	});
});