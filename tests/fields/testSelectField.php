<?php

namespace HMCMB\Tests;

use CMB_Select;

/**
 * Class SelectFieldTestCase
 *
 * @group fields
 */
class SelectFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Select( 'CMB_Select', 'Field', [] );
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
				'select2_options' => [ 'an' => 'option' ],
				'allow_none'      => true,
				'multiple'        => true,
			],
			[
				'options' => [ 'Option 1', 'Option 2', 'Option 3' ],
				'allow_none'      => true,
				'multiple'        => false,
			]
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
