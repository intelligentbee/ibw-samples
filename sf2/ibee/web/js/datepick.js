$(function() {
	$( "#start_date" ).datepicker({
		dateFormat: "yy-mm-dd",
		defaultDate: "-1m",
		changeMonth: false,
		numberOfMonths: 3,
		showCurrentAtPos: 3,
		onClose: function( selectedDate ) {
			$( "#end_date" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#end_date" ).datepicker({
		dateFormat: "yy-mm-dd",
		defaultDate: "-1d",
		changeMonth: false,
		numberOfMonths: 3,
		showCurrentAtPos: 2,
		onClose: function( selectedDate ) {
			$( "#start_date" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});