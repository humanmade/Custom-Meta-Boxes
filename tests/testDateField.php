<?php

class DateFieldsAssetsTestCase extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->old_wp_scripts = isset( $GLOBALS['wp_scripts'] ) ? $GLOBALS['wp_scripts'] : null;
		$GLOBALS['wp_scripts'] = new WP_Scripts();
		$GLOBALS['wp_scripts']->default_version = get_bloginfo( 'version' );
		$GLOBALS['wp_styles'] = new WP_Scripts();
		$GLOBALS['wp_styles']->default_version = get_bloginfo( 'version' );
	}

	function tearDown() {
		$GLOBALS['wp_scripts'] = $this->old_wp_scripts;
		parent::tearDown();
	}

	/**
	 * Test that all required scripts & styles are correctly loaded.
	 */
	function testDateFieldAssets() {

		global $wp_version;

		$field = new CMB_Date_Field( 'foo', 'Title', array(), array( 'format' => 'U', 'store_utc' => false, 'time' => false ) );

		// Register CMB-Scripts as this is a dependency.
		wp_register_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		$field->enqueue_scripts();
		$field->enqueue_styles();

		$scripts_output = get_echo( 'wp_print_scripts' );
		$styles_output  = get_echo( 'wp_print_styles' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Scripts
		$this->assertContains( '/js/field.datetime.js', $scripts_output );
		$this->assertContains( '/js/cmb.js', $scripts_output );
		$this->assertNotContains( CMB_URL . '/js/jquery.timePicker.js', $scripts_output );

		if ( version_compare( $wp_version, '4.1', '>=' ) ) {
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/core$suffix.js", $scripts_output );
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/datepicker$suffix.js", $scripts_output );
		} else {
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/jquery.ui.core$suffix.js", $scripts_output );
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/jquery.ui.datepicker$suffix.js", $scripts_output );
		}

		// Styles
		$this->assertContains( 'css/vendor/jquery-ui/jquery-ui.css', $styles_output );

	}

	/**
	 * Test that all required scripts & styles are correctly loaded.
	 */
	function testDateTimeFieldAssets() {

		global $wp_version;

		$field = new CMB_Date_Field( 'foo', 'Title', array(), array( 'format' => 'U', 'store_utc' => false, 'time' => true ) );

		// Register CMB-Scripts as this is a dependency.
		wp_register_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		$field->enqueue_scripts();
		$field->enqueue_styles();

		$scripts_output = get_echo( 'wp_print_scripts' );
		$styles_output  = get_echo( 'wp_print_styles' );

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Scripts
		$this->assertContains( '/js/field.datetime.js', $scripts_output );
		$this->assertContains( '/js/cmb.js', $scripts_output );
		$this->assertContains( CMB_URL . '/js/jquery.timePicker.min.js', $scripts_output );

		if ( version_compare( $wp_version, '4.1', '>=' ) ) {
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/core$suffix.js", $scripts_output );
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/datepicker$suffix.js", $scripts_output );
		} else {
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/jquery.ui.core$suffix.js", $scripts_output );
			$this->assertContains( site_url() . "/wp-includes/js/jquery/ui/jquery.ui.datepicker$suffix.js", $scripts_output );
		}

		// Styles
		$this->assertContains( 'css/vendor/jquery-ui/jquery-ui.css', $styles_output );

	}

	function testTimeFieldAssets() {

		$field = new CMB_Time_Field( 'foo', 'Title', array() );

		// Register CMB-Scripts as this is a dependency.
		wp_enqueue_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		$field->enqueue_scripts();

		$scripts_output = get_echo( 'wp_print_scripts' );

		// Scripts
		$this->assertContains( CMB_URL . '/js/cmb.js', $scripts_output );
		$this->assertContains( CMB_URL . '/js/jquery.timePicker.min.js', $scripts_output );
		$this->assertContains( CMB_URL . '/js/field.datetime.js', $scripts_output );

	}

}
