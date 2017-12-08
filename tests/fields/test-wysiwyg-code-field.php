<?php
/**
 * Tests for the WYSIWYG field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_wysiwyg;

/**
 * Class TestWYSIWYGField
 *
 * @group fields
 */
class TestWYSIWYGField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_wysiwyg( 'CMB_wysiwyg', 'Field', [] );
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
