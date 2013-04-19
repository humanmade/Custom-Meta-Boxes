/**
 * Controls the behaviours of custom metabox fields.
 *
 * @author Andrew Norcross
 * @author Jared Atchison
 * @author Bill Erickson
 * @author Jonathan Bardo
 * @see     https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
 * @see		https://github.com/jonathanbardo/custom-metaboxes
 */

/*jslint browser: true, devel: true, indent: 4, maxerr: 50, sub: true */

/**
 * Custom jQuery for Custom Metaboxes and Fields
 */
jQuery(document).ready(function ($) {
	'use strict';

	var formfield;
	var formfieldobj;

	jQuery( document ).on( 'click', '.delete-field', function( e ) {

		e.preventDefault();
		var a = jQuery( this );

		var confirmation = true;
		//Confirm group deletion
		if(a.parents('.cmb_repeat_element').length != 0) 
			confirmation = confirm("Do you confirm the deletion of this group ?");

		if(confirmation === true)
			a.closest( '.field-item' ).slideToggle('fast', function(){
				$(this).remove();
			});

	} );

	/**
	 * Initialize timepicker (this will be moved inline in a future release)
	 */
	$('.cmb_timepicker').each(function () {
		$( this ).timePicker({
			startTime: "07:00",
			endTime: "22:00",
			show24Hours: false,
			separator: ':',
			step: 30
		});
	});

	/**
	 * Initialize jQuery UI datepicker (this will be moved inline in a future release)
	 */
	$('.cmb_datepicker').each(function () {
		$( this ).datepicker();
		// $('#' + jQuery(this).attr('id')).datepicker({ dateFormat: 'yy-mm-dd' });
		// For more options see http://jqueryui.com/demos/datepicker/#option-dateFormat
	});
	// Wrap date picker in class to narrow the scope of jQuery UI CSS and prevent conflicts
	$("#ui-datepicker-div").wrap('<div class="cmb_element" />');

	/**
	 * Initialize color picker
	 */
	if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function')
		$('input:text.cmb_colorpicker').wpColorPicker();


	//Trigger wysiwygs
	$('.cmb_wysiwyg:not(:hidden)').each(function(){
		tinyMCE.execCommand('mceAddControl', false, $(this).attr('id') );
	})

	$('.CMB_Group_Field').sortable({
		cancel: '.mceStatusbar', 
		handle: '.move-field',
		items: "> .field-item",
		start: function(event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
			$('.cmb_wysiwyg').each(function(){
				tinyMCE.execCommand('mceRemoveControl', false, $(this).attr('id') );
			})
		},
		stop: function(event, ui) { // re-initialize TinyMCE when sort is completed
			$('.cmb_wysiwyg:not(:hidden)').each(function(){
				tinyMCE.execCommand('mceAddControl', false, $(this).attr('id') );
			})
		}
	});

	jQuery( document ).on( 'click', '.repeat-field', function(event) {
		event.preventDefault();

	    var el = jQuery( this );

	    var newT = el.prev().clone();

	    //Make a colorpicker field repeatable
	    newT.find('.wp-color-result').remove();
		newT.find('input:text.cmb_colorpicker').wpColorPicker();

	    newT.removeClass('hidden');
	    newT.find('input[type!="button"]').val('');
	    newT.find( '.cmb_upload_status' ).html('');
	    newT.css('display', 'none');
	    newT.insertBefore( el.prev() );

	    // Recalculate group ids & update the name fields..
		var index = 0;
		var field = $(this).closest('.field' );
		var attrs = ['id','name','for'];	
		
		field.children('.field-item').not('.hidden').each( function() {

			var search  = field.hasClass( 'CMB_Group_Field' ) ? /cmb-group-(\d|x)*/ : /cmb-field-(\d|x)*/;
			var replace = field.hasClass( 'CMB_Group_Field' ) ? 'cmb-group-' + index : 'cmb-field-' + index;

			$(this).find('[id],[for],[name]').each( function() {

				for ( var i = 0; i < attrs.length; i++ )
					if ( typeof( $(this).attr( attrs[i] ) ) !== 'undefined' )
						$(this).attr( attrs[i], $(this).attr( attrs[i] ).replace( search, replace ) );
				
			} );

			index += 1;

		} );

		newT.slideToggle('fast', function(){
			tinyMCE.execCommand('mceAddControl', false, $(this).find('.cmb_wysiwyg').attr('id') );
		});

	    // Reinitialize all the datepickers
		jQuery('.cmb_datepicker' ).each(function () {
			$(this).attr( 'id', '' ).removeClass( 'hasDatepicker' ).removeData( 'datepicker' ).unbind().datepicker();
		});

		// Reinitialize all the timepickers.
		jQuery('.cmb_timepicker' ).each(function () {
			$(this).timePicker({
				startTime: "07:00",
				endTime: "22:00",
				show24Hours: false,
				separator: ':',
				step: 30
			});
		});

	});

});
