$(document).ready(function() {
	$('#manifest').submit(function(e) {
		e.preventDefault();
		var tickets = [];
		$('.ticket-row').each(function() {
			tickets.push({id         : $(this).attr('id'),
						  c_name     : $(this).find('#c_name').val(),
						  next_of_kin: $(this).find('#next_of_kin').val(),
						  seat_no    : $(this).find('#seat_no').val()
						});
		});
		
		//alert(JSON.stringify(tickets));
		
		$.ajax({
			type: 'POST',
			url : "quick_ajax.php",
			data: 'posted_data=' + encodeURIComponent(JSON.stringify(tickets)),
			success : function(d) {
				location.href = 'reports.php';
			},
			error : function(xhr, i) {
				alert(i);
			}
		});
	});
});