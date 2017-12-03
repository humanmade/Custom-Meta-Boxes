<?php

namespace HMCMB\Tests;

use CMB_Text_Small_Field;

/**
 * Class TextSmallFieldTestCase
 *
 * @group fields
 */
class TextSmallFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Text_Small_Field( 'CMB_Text_Small_Field', 'Field', [] );
	}
}
