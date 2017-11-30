<?php

namespace HMCMB\Tests;

use CMB_Radio_Field;

/**
 * Class RadioFieldTestCase
 *
 * @group fields
 */
class RadioFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Radio_Field( 'field', 'Field', [] );
	}
}
