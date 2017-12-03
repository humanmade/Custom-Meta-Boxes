<?php

namespace HMCMB\Tests;

use CMB_Textarea_Field;

/**
 * Class TextareaFieldTestCase
 *
 * @group fields
 */
class TextareaFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Textarea_Field( 'CMB_Textarea_Field', 'Field', [] );
	}
}
