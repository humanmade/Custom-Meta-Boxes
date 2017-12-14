<?php
/**
 * Tests for the Google Maps field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Gmap_Field;

/**
 * Class TestGmapField
 *
 * @group fields
 */
class TestGmapField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Gmap_Field( 'CMB_Gmap_Field', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'google_api_key' => 'abcdefghijk',
				'default_lat'    => '1.234',
				'default_long'   => '1.234',
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
