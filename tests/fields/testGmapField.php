<?php

namespace HMCMB\Tests;

use CMB_Gmap_Field;

/**
 * Class GmapFieldTestCase
 *
 * @group fields
 */
class GmapFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Gmap_Field( 'field', 'Field', [] );
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
			]
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
