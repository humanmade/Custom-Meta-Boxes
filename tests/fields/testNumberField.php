<?php
/**
 * Tests for the number field type.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Number_Field;

/**
 * Class NumberFieldTestCase
 *
 * @group fields
 */
class NumberFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Number_Field( 'CMB_Number_Field', 'Field', [] );
	}

	function testSaveValue() {
		$value = array( 0.5 );
		$field = new CMB_Number_Field( 'foo', 'Foo', $value, array( 'min' => 0, 'max' => 1 ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( self::$post->ID, $value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( self::$post->ID, 'foo', false ), $value );
	}

	function testFieldOutput() {
		$field = new CMB_Number_Field( 'foo', 'Foo', array( 0.5 ), array( 'min' => 0.4, 'max' => 1 ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/min="0.4".*max="1".*(type=\"number\".*?id=\"foo-cmb-field-0\".*?value=\"0.5\")/s' );

		// Trigger output.
		$field->html();
	}
}
