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
}
