<?php

namespace HMCMB\Tests;

use CMB_Radio_Field;

/**
 * Class RadioFieldTestCase
 *
 * @group fields
 */
class RadioFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Radio_Field( 'CMB_Radio_Field', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'options' => [ 'Option 1', 'Option 2', 'Option 3' ],
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
