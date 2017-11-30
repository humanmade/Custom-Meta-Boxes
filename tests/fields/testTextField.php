<?php

namespace HMCMB\Tests;

use CMB_Text_Field;

/**
 * Class TextFieldTestCase
 *
 * @group fields
 */
class TextFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Text_Field( 'field', 'Field', [] );
	}
}
