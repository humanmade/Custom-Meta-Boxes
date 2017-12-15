<?php
/**
 * Tests for the plain textarea field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Textarea_Field;

/**
 * Class TestTextareaField
 *
 * @group fields
 */
class TestTextareaField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Textarea_Field( 'CMB_Textarea_Field', 'Field', [] );
	}
}
