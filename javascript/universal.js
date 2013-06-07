var BASE_URL = "http://localhost/ticket/";

$(document).ready(function() {
	
	var calendar = new dhtmlXCalendarObject(["t_date"]);
	from_date = (new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + (Number(new Date().getDate() + 1))).toString();
	to_date   = (new Date().getFullYear() + '-' + (new Date().getMonth() + 1) + '-' + (Number(new Date().getDate() + 14))).toString();
	calendar.setSensitiveRange(from_date, to_date);
	calendar.setDate(from_date);
});


function popup() {
	$("#modal-content").modal();
	return false;
}

