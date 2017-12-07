<?php
/**
 * Tests for the textarea-saved-as-code field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

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
