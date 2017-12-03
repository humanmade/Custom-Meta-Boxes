<?php

namespace HMCMB\Tests;

use CMB_URL_Field;

/**
 * Class URLFieldTestCase
 *
 * @group fields
 */
class URLFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_URL_Field( 'CMB_URL_Field', 'Field', [] );
	}
}
