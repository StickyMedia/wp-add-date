(function($) {
	$(function() {
		
		// Check to make sure the input box exists
		if( 0 < $('#datepicker').length ) {
			$('#datepicker').datetimepicker({dateFormat: 'yy-mm-dd'});
		}
	});
}(jQuery));