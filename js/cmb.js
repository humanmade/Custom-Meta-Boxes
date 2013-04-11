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
			a.closest( '.field-item' ).slideToggle('normal', function(){
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
	if (typeof jQuery.wp === 'object' && typeof jQuery.wp.wpColorPicker === 'function') {
		$('input:text.cmb_colorpicker').wpColorPicker();
	} else {
		$('input:text.cmb_colorpicker').each(function (i) {
			$(this).after('<div id="picker-' + i + '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>');
			$('#picker-' + i).hide().farbtastic($(this));
		})
		.focus(function () {
			$(this).next().show();
		})
		.blur(function () {
			$(this).next().hide();
		});
	}

	// Make group sortable
	var textarea_id;
	var id = 1;
	trigger_wysiwygs($('.CMB_wysiwyg:not(:hidden)'));

	function trigger_wysiwygs($wysiwygs){
		var $object;
		$wysiwygs.each(function(){
			id++;
			$object = $(this).find('textarea');
			$object.attr('id', $object.attr('id') + '-' + id);
			textarea_id = $object.attr('id');

			//Add toggle to go back to textarea
			$(this).find('.field-title').after("<a href='javascript:void(0);' class='button togglewysiwyg ui-state-default' data-id='"+textarea_id+"'' style='margin-bottom:10px;'>⇄ Toggle Editor</a>");
			
		});
	}

	function trigger_toggle_wysiwygs(){
		 $(".togglewysiwyg").toggle(
			function(event){
				tinyMCE.execCommand('mceAddControl', false, $(this).data('id'));
			},
			function(){
				tinyMCE.execCommand('mceRemoveControl', false, $(this).data('id'));
			}
		);
		$(".togglewysiwyg").trigger('click');
	}

	trigger_toggle_wysiwygs();

	$('.CMB_Group_Field').sortable({
		cancel: '.mceStatusbar', 
		handle: '.move-field',
		items: "> .field-item",
		start: function(event, ui) { // turn TinyMCE off while sorting (if not, it won't work when resorted)
			tinyMCE.execCommand('mceRemoveControl', false, textarea_id);
		},
		stop: function(event, ui) { // re-initialize TinyMCE when sort is completed
			tinyMCE.execCommand('mceAddControl', false, textarea_id);
		}
	});

	jQuery( document ).on( 'click', '.repeat-field', function(event) {
		event.preventDefault();

	    var el = jQuery( this );

	    var newT = el.prev().clone();

	    //Make a colorpicker field repeatable
	    newT.find('.wp-color-result').remove();
		newT.find('input:text.cmb_colorpicker').wpColorPicker();
		var $wysiwygs = newT.find('.CMB_wysiwyg');
		trigger_wysiwygs($wysiwygs);

	    newT.removeClass('hidden');
	    newT.find('input[type!="button"]').val('');
	    newT.find( '.cmb_upload_status' ).html('');
	    newT.css('display', 'none');
	    newT.insertBefore( el.prev() );
		newT.slideToggle('normal');
	    //Toggle wysiwyg at the end
	    trigger_toggle_wysiwygs();

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
