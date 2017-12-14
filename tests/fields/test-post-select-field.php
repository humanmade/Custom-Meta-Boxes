<?php
/**
 * Tests for the post select field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Post_Select;

/**
 * Class TestPostSelectField
 *
 * @group fields
 */
class TestPostSelectField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Post_Select( 'CMB_Post_Select', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'use_ajax' => true,
			],
			[
				'use_ajax' => true,
				'multiple' => true,
			],
			[
				'use_ajax' => true,
				'multiple' => true,
				'query'    => [
					'posts_per_page' => 20,
				],
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
