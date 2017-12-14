<?php
/**
 * Tests for the time field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Time_Field;

/**
 * Class TestTimeField
 *
 * @group fields
 */
class TestTimeField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Time_Field( 'CMB_Time_Field', 'Field', [] );
	}

	/**
	 * Test that all required scripts & styles are correctly loaded.
	 */
	function testAssets() {
		$this->reset_wp_scripts();

		// Register CMB-Scripts as this is a dependency.
		wp_enqueue_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		$this->instance->enqueue_scripts();

		$scripts_output = get_echo( 'wp_print_scripts' );

		// Scripts
		$this->assertContains( CMB_URL . '/js/cmb.js', $scripts_output );
		$this->assertContains( CMB_URL . '/js/jquery.timePicker.min.js', $scripts_output );
		$this->assertContains( CMB_URL . '/js/field.datetime.js', $scripts_output );
	}
}
