<?php

namespace HMCMB\Tests;

use CMB_Color_Picker;

/**
 * Class ColorFieldTestCase
 *
 * @group fields
 */
class ColorFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Color_Picker( 'field', 'Field', [] );
	}
}
