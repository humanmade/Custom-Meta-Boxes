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

		$this->instance = new CMB_Post_Select( 'field', 'Field', [] );
	}
}
