<?php

namespace HMCMB\Tests;

use CMB_Date_Field;
use WP_Scripts;

/**
 * Class DateFieldsAssetsTestCase
 *
 * @group fields
 */
class DateFieldsAssetsTestCase extends TestFieldCase {
	function setUp() {
		parent::setUp();

		$this->instance = new CMB_Date_Field( 'field', 'Field', [] );;

		$this->old_wp_scripts = isset( $GLOBALS['wp_scripts'] ) ? $GLOBALS['wp_scripts'] : null;
		$GLOBALS['wp_scripts'] = new WP_Scripts();
		$GLOBALS['wp_scripts']->default_version = get_bloginfo( 'version' );
	}

	function tearDown() {
		$GLOBALS['wp_scripts'] = $this->old_wp_scripts;
		parent::tearDown();
	}

	/**
	 * Test that all required scripts & styles are correctly loaded.
	 */
	function testAssets() {
		global $wp_version;

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
