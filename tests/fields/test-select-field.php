<?php
/**
 * Tests for the select field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Select;

/**
 * Class TestSelectField
 *
 * @group fields
 */
class TestSelectField extends TestFieldCase {
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
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
