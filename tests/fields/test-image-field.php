<?php

namespace HMCMB\Tests;

use CMB_Image_Field;

/**
 * Class TestImageField
 *
 * @group fields
 */
class TestImageField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Image_Field( 'CMB_Image_Field', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'size' => 175,
			],
			[
				'size'      => 175,
				'show_size' => true,
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
