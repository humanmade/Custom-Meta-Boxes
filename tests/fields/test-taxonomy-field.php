<?php
/**
 * Tests for the taxonomy field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Taxonomy;

/**
 * Class TestTaxonomyField
 *
 * @group fields
 */
class TestTaxonomyField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Taxonomy( 'CMB_Taxonomy', 'Field', [] );
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'taxonomy'   => 'post_tag',
				'hide_empty' => false,
				'multiple'   => true,
			],
			[
				'taxonomy'   => 'post_tag',
				'hide_empty' => true,
				'multiple'   => true,
			],
			[
				'taxonomy'   => 'post_tag',
				'hide_empty' => false,
				'multiple'   => false,
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
