<?php
/**
 * Tests for the small-sized text field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Text_Small_Field;

/**
 * Class TestTextSmallField
 *
 * @group fields
 */
class TestTextSmallField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Text_Small_Field( 'CMB_Text_Small_Field', 'Field', [] );
	}
}
