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

		$this->instance = new CMB_Select( 'field', 'Field', [] );
	}
}
