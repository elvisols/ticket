// JavaScript Document
$(function(){
		$('input[name="payment-mode"]').bind('change', function() {
			$('table.payment').hide();
			$('table.'+$(this).val()).slideDown('slow');
		});
	}
);