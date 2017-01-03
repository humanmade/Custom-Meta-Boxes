<?php
/**
 * Example functions to reference for developers.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes Existing metaboxes.
 * @return array
 */
function cmb_sample_metaboxes( array $meta_boxes ) {

	/**
	 * Example of all available fields.
	 */
	$fields = array(

		/*
		 * Basic Text Field.
		 */
		array(
			'id'   => 'field-1',
			'name' => 'Text input field',
			'type' => 'text',
		),

		/*
		 * Text Field with all options.
		 */
		array(
			// Required. ID of CMB value to be inserted into database.
			'id'              => 'field-2',

			// Required.Type of field to call.
			'type'            => 'text',

			// Optional. Title that will show above field.
			'name'            => __( 'Name', 'cmb' ),

			// Optional. Contextual information. Will display underneath field.
			'desc'            => __( 'Repeatable', 'cmb' ),

			// Optional. Whether field should be repeatable.
			'repeatable'      => true,

			// Optional. Maximum number of repetitions allowed.
			'repeatable_max'  => 5,

			// Optional. Whether repeated instances should be sortable.
			'sortable'        => true,

			// Optional. Don't display field label if set to false.
			'show_label'      => false,

			// Optional. Make a filed uneditable by user but value will still be saved.
			'readonly'        => false,

			// Optional. Make field uneditable by user and value will NOT be saved.
			'disabled'        => false,

			// Optional. Default value for new posts.
			'default'         => '',

			// Optional. Number of columns field should take up out of 12.
			'cols'            => '12',

			// Optional. Add custom styles to field.
			'style'           => '',

			// Optional. Add custom CSS classes to field.
			'class'           => '',

			// Optional. Custom data callback for field.
			'data_delegate'   => null,

			// Optional. Custom save method for field.
			'save_callback'   => null,
		),

		/*
		 * Small Text Field.
		 */
		array(
			'id'   => 'field-3',
			'name' => 'Small text input field',
			'type' => 'text_small',
		),

		/*
		 * URL Field.
		 */
		array(
			'id'   => 'field-4',
			'name' => 'URL field',
			'type' => 'url',
		),

		/*
		 * Radio Input Field.
		 */
		array(
			'id'      => 'field-5',
			'name'    => 'Radio input field',
			'type'    => 'radio',
			'options' => array( // Options for individual radio inputs.
				'Option 1',
				'Option 2',
			),
		),

		/*
		 * Single Checkbox Field.
		 */
		array(
			'id'    => 'field-6',
			'name' => 'Checkbox field',
			'type' => 'checkbox',
		),

		/*
		 * WYSIWYG (What You See is What You Get) TinyMCE Field.
		 */
		array(
			'id'         => 'field-7',
			'name'       => 'WYSIWYG field',
			'type'       => 'wysiwyg',
			'options'    => array( // Options to pass into TinyMCE instance.
				'editor_height' => '100',
			),
		),

		/*
		 * Textarea Field.
		 */
		array(
			'id'   => 'field-8',
			'name' => 'Textarea field',
			'type' => 'textarea',
		),

		/*
		 * Textarea Code Field.
		 */
		array(
			'id'   => 'field-9',
			'name' => 'Code textarea field',
			'type' => 'textarea_code',
		),

		/*
		 * File Upload Field.
		 */
		array(
			'id'           => 'field-10',
			'name'         => 'File field',
			'type'         => 'file',
			'library-type' => array( // Optional. Type of media allowed [ 'audio', 'video', 'text', 'application' ].
				'video',
			),
		),

		/*
		 * Image Upload Field.
		 */
		array(
			'id'         => 'field-11',
			'name'       => 'Image upload field',
			'type'       => 'image',
			'repeatable' => true,
			'size'       => 'thumbnail', // Optional. Registered media size to display.
			'show_size'  => true,        // Optional. Whether to show the image dimensions underneath image itself.
		),

		/*
		 * Select Field.
		 */
		array(
			'id'              => 'field-12',
			'name'            => 'Select field',
			'type'            => 'select',
			'options'         => array(
				'option-1' => 'Option 1',
				'option-2' => 'Option 2',
				'option-3' => 'Option 3',
			),
			'allow_none'      => true,    // Optional. Allow a user to not select any options.
			'multiple'        => true,    // Optional. Allow multi-select.
			'select2_options' => array(), // Optional. Array of options to pass to Select2.
		),

		/*
		 * Select for Taxonomies Field.
		 */
		array(
			'id'         => 'field-13',
			'name'       => 'Select taxonomy field',
			'type'       => 'taxonomy_select',
			'taxonomy'   => 'category', // Required. Name of taxonomy to pull options from.
			'hide_empty' => false,      // Optional. Whether to hide empty terms or not.
			'multiple'   => true,       // Optional. Allow multi-select.

		),

		/*
		 * Select for Posts Field.
		 */
		array(
			'id'       => 'field-14',
			'name'     => 'Post select field',
			'type'     => 'post_select',
			'use_ajax' => false,  // Optional. Use AJAX for instant results or load on pageload.
			'multiple' => true,   // Optional. Allow multi-select.
			'query'    => array(  // Optional. WP_Query options to pass through.
				'cat' => 1,
			),
		),

		/*
		 * Basic Date Field.
		 */
		array(
			'id'   => 'field-18',
			'name' => 'Date input field',
			'type' => 'date',
		),

		/*
		 * Basic Time Field.
		 */
		array(
			'id'    => 'field-19',
			'name' => 'Time input field',
			'type' => 'time',
		),

		/*
		 * Date UNIX Field.
		 */
		array(
			'id'   => 'field-20',
			'name' => 'Date (unix) input field',
			'type' => 'date_unix',
		),

		/*
		 * Date & Time UNIX Field.
		 */
		array(
			'id'   => 'field-21',
			'name' => 'Date & Time (unix) input field',
			'type' => 'datetime_unix',
		),

		/*
		 * Colorpicker Field.
		 */
		array(
			'id'   => 'field-22',
			'name' => 'Color',
			'type' => 'colorpicker',
		),

		/*
		 * Google Map Field.
		 */
		array(
			'id'                          => 'field-23',
			'name'                        => 'Location',
			'type'                        => 'gmap',

			// Required. Pass a Google Maps API key. Alternatively, set with CMB_GAPI_KEY constant.
			'google_api_key'              => '{CUSTOM_KEY}',

			// Optional. Width of address input + map display.
			'field_width'                 => '100%',

			// Optional. Static height of address input + map display.
			'field_height'                => '250px',

			// Optional Default latitude for map to display on page load.
			'default_lat'                 => '51.5073509',

			// Optional. Default longitude for map to display on page load.
			'default_long'                => '-0.12775829999998223',

			// Optional. Default zoom for map to display on page load. 1 = Whole world, 20 = Individual buildings
			'default_zoom'                => '8',

			// Optional. Override Marker title text.
			'string-marker-title'         => esc_html__( 'Drag to set the exact location', 'cmb' ),

			// Optional. Override maps error message.
			'string-gmaps-api-not-loaded' => esc_html__( 'Google Maps API not loaded.', 'cmb' ),
		),

		/*
		 * Plain Title Field.
		 */
		array(
			'id'   => 'field-24',
			'name' => 'Title Field',
			'type' => 'title',
		),

	);

	/**
	 * Metabox instantiation.
	 */
	$meta_boxes[] = array(
		'title' => 'CMB Test - all fields',
		'pages' => 'post',
		'fields' => $fields,
	);

	/**
	 * Examples of Groups and Columns.
	 */
	$groups_and_cols = array(

		array(
			'id' => 'gac-1',
			'name' => 'Text input field',
			'type' => 'text',
			'cols' => 4,
		),

		array(
			'id' => 'gac-2',
			'name' => 'Text input field',
			'type' => 'text',
			'cols' => 4,
		),

		array(
			'id' => 'gac-3',
			'name' => 'Text input field',
			'type' => 'text',
			'cols' => 4,
		),

		/**
		 * Group within a Group.
		 */
		array(
			'id'     => 'gac-4',
			'name'   => 'Group (4 columns)',
			'type'   => 'group',
			'cols'   => 4,
			'fields' => array(
				array(
					'id' => 'gac-4-f-1',
					'name' => 'Textarea field',
					'type' => 'textarea',
				),
			),
		),

		array(
			'id'     => 'gac-5',
			'name'   => 'Group (8 columns)',
			'type'   => 'group',
			'cols'   => 8,
			'fields' => array(
				array(
					'id' => 'gac-4-f-1',
					'name' => 'Text input field',
					'type' => 'text',
				),
				array(
					'id' => 'gac-4-f-2',
					'name' => 'Text input field',
					'type' => 'text',
				),
			),
		),
	);

	$meta_boxes[] = array(
		'title'  => 'Groups and Columns',
		'pages'  => 'post',
		'fields' => $groups_and_cols,
	);

	/**
	 * Example of repeatable group. Using all fields.
	 * For this example, copy fields from $fields, update ID.
	 */

	$group_fields = $fields;
	foreach ( $group_fields as &$field ) {
		$field['id'] = str_replace( 'field', 'gfield', $field['id'] );
	}

	$meta_boxes[] = array(
		'title' => 'CMB Test - group (all fields)',
		'pages' => 'post',
		'fields' => array(
			array(
				'id'         => 'gp',
				'name'       => 'My Repeatable Group',
				'type'       => 'group',
				'repeatable' => true,
				'sortable'   => true,
				'fields'     => $group_fields,
				'desc'       => 'This is the group description.',
			),
		),
	);

	return $meta_boxes;

}
add_filter( 'cmb_meta_boxes', 'cmb_sample_metaboxes' );
