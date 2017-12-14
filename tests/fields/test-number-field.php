<?php
/**
 * Tests for the number field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Number_Field;

/**
 * Class TestNumberField
 *
 * @group fields
 */
class TestNumberField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Number_Field( 'CMB_Number_Field', 'Field', [] );
	}

	/**
	 * Test that the number field outputs correctly against more specific field output.
	 */
	function testFieldOutput() {
		$field = new CMB_Number_Field( 'foo', 'Foo', array( 0.5 ), array( 'min' => 0.4, 'max' => 1 ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/min="0.4".*max="1".*(type=\"number\".*?id=\"foo-cmb-field-0\".*?value=\"0.5\")/s' );

		// Trigger output.
		$field->html();
	}

	/**
	 * Provide a default set of values to test saving against.
	 *
	 * @return array Default values set.
	 */
	public function valuesProvider() {
		return [
			[ [ '1' ] ],
			[ [ 162735 ] ],
			[ [ 0.5 ] ],
		];
	}
}
