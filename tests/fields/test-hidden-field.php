<?php
/**
 * Tests for the hidden field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Hidden_Field;

/**
 * Class TestHiddenField
 *
 * @group fields
 */
class TestHiddenField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Hidden_Field( 'CMB_Hidden_Field', 'Field', [] );
	}

	/**
	 * Test that the number field outputs correctly against more specific field output.
	 */
	function testFieldOutput() {
		$field = new CMB_Hidden_Field( 'foo', 'Foo', array( 'value' ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/(type=\"hidden\".*?id=\"foo-cmb-field-0\".*?value=\"value\")/s' );

		// Trigger output.
		$field->html();
	}
}
