<?php
/**
 * Tests for the date & time/UNIX timestamp field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Datetime_Timestamp_Field;

/**
 * Class TestDateTimeTimestampField
 *
 * @group fields
 */
class TestDateTimeTimestampField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Datetime_Timestamp_Field( 'CMB_Datetime_Timestamp_Field', 'Field', [] );
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

	/**
	 * Provide a default set of values to test saving against.
	 *
	 * @return array Default values set.
	 */
	public function valuesProvider() {
		return [
			[
				[
					[
						'date' => '12/12/2012',
						'time' => '12:00 am',
					],
				],
				[ '1355270400' ],
			],
			[
				[
					[
						'date' => '12/12/2112',
						'time' => '12:00 am',
					],
				],
				[ '4510944000' ],
			],
		];
	}
}
