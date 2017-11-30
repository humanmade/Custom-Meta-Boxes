<?php

namespace HMCMB\Tests;

use CMB_wysiwyg;

/**
 * Class WYSIWYGFieldTestCase
 *
 * @group fields
 */
class WYSIWYGFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_wysiwyg( 'field', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'options' => [ 'editor_height' => 500 ],
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
