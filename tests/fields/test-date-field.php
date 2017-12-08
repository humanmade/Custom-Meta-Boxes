<?php
/**
 * Tests for the plain date field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Date_Field;

/**
 * Class TestDateFieldsAssets
 *
 * @group fields
 */
class TestDateFieldsAssets extends TestFieldCase {
	function setUp() {
		parent::setUp();

		$this->instance = new CMB_Date_Field( 'CMB_Date_Field', 'Field', [] );
	}

	/**
	 * Test that all required scripts & styles are correctly loaded.
	 */
	function testAssets() {
		global $wp_version;

		$this->reset_wp_scripts();

		// Register CMB-Scripts as this is a dependency.
		wp_register_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		$this->instance->enqueue_scripts();
		$this->instance->enqueue_styles();

		$scripts_output = get_echo( 'wp_print_scripts' );
		$styles_output = get_echo( 'wp_print_styles' );

		// Scripts
		$this->assertContains( '/js/field.datetime.js', $scripts_output );
		$this->assertContains( '/js/cmb.js', $scripts_output );
		if ( version_compare( $wp_version, '4.1', '>=' ) ) {
			$this->assertContains( site_url() . '/wp-includes/js/jquery/ui/core.min.js', $scripts_output );
			$this->assertContains( site_url() . '/wp-includes/js/jquery/ui/datepicker.min.js', $scripts_output );
		} else {
			$this->assertContains( site_url() . '/wp-includes/js/jquery/ui/jquery.ui.core.min.js', $scripts_output );
			$this->assertContains( site_url() . '/wp-includes/js/jquery/ui/jquery.ui.datepicker.min.js', $scripts_output );
		}

		// Styles
		$this->assertContains( 'css/vendor/jquery-ui/jquery-ui.css', $styles_output );
	}
}
