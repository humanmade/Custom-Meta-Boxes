(function($) {

	/**
	 * Date & Time Fields
	 */

	function CMBDateFieldInit( $container ) {

		var $dateFields, $timeFields;

		if ( $container ) {
			$dateFields = $container.find( '.cmb_datepicker' );
			$timeFields = $container.find( '.cmb_timepicker' );
		} else {
			$dateFields = $( '.cmb_datepicker' )
			$timeFields = $( '.cmb_timepicker' )
		}

		// Reinitialize all the datepickers
		$dateFields.each(function () {

			var val, field;

			field = $( this );

			field.datepicker({
				altFormat: "yy-mm-dd",
				altField: field.data( 'alt-field' ),
				dateFormat: 'd M yy',
			});

			if ( val = $( field.data( 'alt-field' ) ).val() ) {
				field.datepicker( "setDate", new Date( val ) );
			}

		});

		// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
		$("#ui-datepicker-div").wrap('<div class="cmb_element"/>');

		// Timepicker
		$timeFields.each(function () {
			jQuery(this).timePicker({
				startTime: "00:00",
				endTime: "23:30",
				show24Hours: false,
				separator: ':',
				step: 30
			});
		} );

	}

	CMB.addCallbackForClonedField( ['CMB_Date_Field', 'CMB_Time_Field' ], function( $container ) {
		CMBDateFieldInit( $container );
	} );

	CMB.addCallbackForInit( function() {
		CMBDateFieldInit();
	});



	// // Reinitialize all the datepickers
	// newT.find( '.cmb_datepicker' ).each(function () {

	// 	var field = jQuery( this );
	// 	var altField = jQuery( $(this).data( 'alt-field' ) );

	// 	jQuery(this).datepicker({
	// 		// altFormat: "yy-mm-dd",
	// 		// altField: altField,
	// 		// dateFormat: 'd MM yy',
	// 		dateFormat: 'yy',
	// 	});
	// });

	// // Reinitialize all the timepickers.
	// newT.find('.cmb_timepicker' ).each(function () {
	// 	jQuery(this).timePicker({
	// 		startTime: "00:00",
	// 		endTime: "23:30",
	// 		show24Hours: false,
	// 		separator: ':',
	// 		step: 30
	// 	});
	// });

}(jQuery));
