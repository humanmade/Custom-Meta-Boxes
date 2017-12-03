<?php

namespace HMCMB\Tests;

use CMB_Taxonomy;

/**
 * Class TaxonomyFieldTestCase
 *
 * @group fields
 */
class TaxonomyFieldTestCase extends TestFieldCase {
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
