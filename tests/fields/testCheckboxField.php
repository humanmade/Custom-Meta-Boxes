<?php

namespace HMCMB\Tests;

use CMB_Checkbox;

/**
 * Class CheckboxFieldTestCase
 *
 * @group fields
 */
class CheckboxFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Checkbox( 'CMB_Checkbox', 'Field', [] );
	}
}
