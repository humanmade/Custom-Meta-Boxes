<?php
/*
Plugin Name: Custom Meta Boxes
Plugin URI: https://github.com/humanmade/Custom-Meta-Boxes
Description: Lets you easily create metaboxes with custom fields that will blow your mind. Originally a fork of https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress.
Version: 1.0.3
License: GPL-2.0+
Author: Human Made Limited
Author URI: http://hmn.md

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'CMB_VERSION' ) ) {
	define( 'CMB_VERSION', '1.0.3' );
}

if ( ! defined( 'CMB_DEV' ) ) {
	define( 'CMB_DEV', false );
}

if ( ! defined( 'CMB_PATH' ) ) {
	define( 'CMB_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CMB_URL' ) ) {
	define( 'CMB_URL', plugins_url( '', __FILE__ ) );
}

/**
 * Include base, required files.
 */
include_once( CMB_PATH . 'class-cmb-meta-box.php' );

/**
 * Field types that are extended.
 */
include_once( CMB_PATH . 'fields/class-cmb-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-file-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-select.php' );
include_once( CMB_PATH . 'fields/class-cmb-text-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-textarea-field.php' );

/**
 * Final field types.
 */
include_once( CMB_PATH . 'fields/class-cmb-checkbox.php' );
include_once( CMB_PATH . 'fields/class-cmb-checkbox-multi.php' );
include_once( CMB_PATH . 'fields/class-cmb-color-picker.php' );
include_once( CMB_PATH . 'fields/class-cmb-date-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-date-timestamp-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-datetime-timestamp-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-email-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-gmap-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-group-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-hidden-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-image-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-number-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-post-select.php' );
include_once( CMB_PATH . 'fields/class-cmb-radio-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-taxonomy.php' );
include_once( CMB_PATH . 'fields/class-cmb-text-small-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-textarea-field-code.php' );
include_once( CMB_PATH . 'fields/class-cmb-time-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-title.php' );
include_once( CMB_PATH . 'fields/class-cmb-url-field.php' );
include_once( CMB_PATH . 'fields/class-cmb-wysiwyg.php' );

/**
 * Make it possible to add fields in locations other than post edit screen. Optional.
 */
include_once( CMB_PATH . '/fields-anywhere.php' );

/**
 * Get all the meta boxes on init.
 */
function cmb_init() {

	// Only run in the admin.
	if ( ! is_admin() ) {
		return;
	}

	// Load translations.
	$textdomain = 'cmb';
	$locale = apply_filters( 'plugin_locale', get_locale(), $textdomain );

	// By default, try to load language files from /wp-content/languages/custom-meta-boxes/
	load_textdomain( $textdomain, WP_LANG_DIR . '/custom-meta-boxes/' . $textdomain . '-' . $locale . '.mo' );
	load_textdomain( $textdomain, CMB_PATH . '/languages/' . $textdomain . '-' . $locale . '.mo' );

	$meta_boxes = apply_filters( 'cmb_meta_boxes', array() );

	// Ensure that we have metaboxes to process.
	if ( ! empty( $meta_boxes ) ) {
		foreach ( $meta_boxes as $meta_box ) {
			new CMB_Meta_Box( $meta_box );
		}
	}

}
add_action( 'init', 'cmb_init', 50 );

/**
 * Return an array of built in available fields.
 *
 * Key is field name, Value is class used by field.
 * Available fields can be modified using the 'cmb_field_types' filter.
 *
 * @return array List of all available fields.
 */
function _cmb_available_fields() {

	/**
	 * Filter and potentially modify the available field types.
	 *
	 * @param $types All available field types.
	 */
	return apply_filters( 'cmb_field_types', array(
		'text'            => 'CMB_Text_Field',
		'text_small'      => 'CMB_Text_Small_Field',
		'text_url'        => 'CMB_URL_Field',
		'email'           => 'CMB_Email_Field',
		'url'             => 'CMB_URL_Field',
		'radio'           => 'CMB_Radio_Field',
		'checkbox'        => 'CMB_Checkbox',
		'checkbox_multi'  => 'CMB_Checkbox_Multi',
		'file'            => 'CMB_File_Field',
		'hidden'          => 'CMB_Hidden_Field',
		'image'           => 'CMB_Image_Field',
		'wysiwyg'         => 'CMB_wysiwyg',
		'textarea'        => 'CMB_Textarea_Field',
		'textarea_code'   => 'CMB_Textarea_Field_Code',
		'select'          => 'CMB_Select',
		'taxonomy_select' => 'CMB_Taxonomy',
		'post_select'     => 'CMB_Post_Select',
		'date'            => 'CMB_Date_Field',
		'date_unix'       => 'CMB_Date_Timestamp_Field',
		'datetime_unix'   => 'CMB_Datetime_Timestamp_Field',
		'time'            => 'CMB_Time_Field',
		'colorpicker'     => 'CMB_Color_Picker',
		'title'           => 'CMB_Title',
		'group'           => 'CMB_Group_Field',
		'gmap'            => 'CMB_Gmap_Field',
		'number'          => 'CMB_Number_Field',
	) );

}

/**
 * Get a field class by type.
 *
 * @param  string $type Type of field (i.e. text, number, radio).
 * @return string|bool $class, or false if not found.
 */
function _cmb_field_class_for_type( $type ) {

	$map = _cmb_available_fields();

	if ( isset( $map[ $type ] ) ) {
		return $map[ $type ];
	}

	return false;

}

/**
 * For the order of repeatable fields to be guaranteed, orderby meta_id needs to be set.
 * Note usermeta has a different meta_id column name.
 *
 * Only do this for older versions as meta is now ordered by ID (since 3.8)
 * See http://core.trac.wordpress.org/ticket/25511
 *
 * @param  string $query SQL query.
 * @return string $query (Potentially) modified query string
 */
function cmb_fix_meta_query_order( $query ) {

	$pattern = '/^SELECT (post_id|user_id), meta_key, meta_value FROM \w* WHERE post_id IN \([\d|,]*\)$/';

	if (
		0 === strpos( $query, 'SELECT post_id, meta_key, meta_value' ) &&
		preg_match( $pattern, $query, $matches )
	) {
		if ( isset( $matches[1] ) && 'user_id' == $matches[1] ) {
			$meta_id_column = 'umeta_id';
		} else {
			$meta_id_column = 'meta_id';
		}

		$meta_query_orderby = ' ORDER BY ' . $meta_id_column;

		if ( false === strpos( $query, $meta_query_orderby ) ) {
			$query .= $meta_query_orderby;
		}
	}

	return $query;

}

/**
 * Only run query modification for older versions as meta is now ordered by ID (since 3.8)
 * See http://core.trac.wordpress.org/ticket/25511
 */
if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
	add_filter( 'query', 'cmb_fix_meta_query_order', 1 );
}
