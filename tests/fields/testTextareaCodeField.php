<?php

namespace HMCMB\Tests;

use CMB_Textarea_Field_Code;

/**
 * Class TextareaCodeFieldTestCase
 *
 * @group fields
 */
class TextareaCodeFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Textarea_Field_Code( 'CMB_Textarea_Field_Code', 'Field', [] );
	}
}
