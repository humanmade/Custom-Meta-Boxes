<?php

namespace HMCMB\Tests;

use CMB_Post_Select;

/**
 * Class PostSelectFieldTestCase
 *
 * @group fields
 */
class PostSelectFieldTestCase extends TestFieldCase {
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
			]
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
