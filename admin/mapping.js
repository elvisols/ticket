$(document).ready(function() {
	$("#origin, #destination").focus(function() {
		var opt = $(this).attr('id');
		if (opt == 'destination' && ($("#origin").val() != "")) {
			$("#origin").attr('disabled', 'disabled');
			$(this).autocomplete('../includes/autocomplete.php?op=get_states').result(function(event, data, formatted) {
				//data = data.toString();
				//var stateId = data.split("%&*")[1];
				//$("#destination").data('id', stateId);
			});
		} else {
			$(this).autocomplete('../includes/autocomplete.php?op=get_states').result(function(event, data, formatted) {
				//data = data.toString();
				//var stateId = data.split("%&*")[1];
				//$("#origin").data('id', stateId);
			});
		}
	});
	
	/*** Add route to travel [ Select travel handler ] ***/
	$("#show").on('focus', '.add_route', function() {
		$(this).autocomplete('../includes/autocomplete.php?op=travel').result(function(event, data, formatted) {
			data = data.toString();
			var id = data.split("%&*")[1];
			$(this).data('id', id);
		});
	});
	
	/*** Add route to travel [ main add handler ] ***/
	$("#show").on('click', '.add_travel', function() {
		var route_code = $(this).parents('div').attr('id');
		var travel_id = $(this).parents('span').find("input[id^=travel_]").data('id');
		$.post('ajax.php', {'route_code':route_code, 'travel_id':travel_id, op:'add_route_to_travel'});
	});
	
	/*** Get all the destinations for the selected origin ***/
	$("#origin").keypress(function(e) {
		if (e.which === 13) {
			$.get('ajax.php', {op:'mapped', state: $("#origin").val()}, function(d) {
				$("#show").html(d);
			});
		}
	});
	
	/*** Add a destination to a source/state/town ***/
	$("#add").click(function() {
		var destination = $('#destination').val();
		var origin = $("#origin").val();
		
		$.post('ajax.php', {'op':'add_route', 'origin':origin, 'destination':destination}, function(d) {
			$("#show").append("<div id='" + d + "'> >> " + destination
				+"<span><input type='text' id='travel_" + d + "' class='add_route' /><input type='button' value='Ok' class='add_travel' />"
				+"<a class='remove_state' id='" + d +"' href='#'>X</a></span></div>\n");
		});
	});
	
	$("#done").click(function() {
		$("#origin").removeAttr("disabled").val('');
		$("#destination").val('');
		$('#show').html('');
	});
	
	/*** Remove a route ***/
	$("#show").on('click', '.remove_state', function() {
		var id = $(this).attr('id');
		$.get('ajax.php', {op:'remove_map', 'map_id':id});
		$('div#' + id).fadeOut();
	});
	
	/*** Add bus ***/
	$("#add_bus").submit(function(e) {
		e.preventDefault();
		var data = $(this).serialize();
		$.post('ajax.php', data + '&op=add_bus');
		$('input[type=reset]').click();
		
	});
});
