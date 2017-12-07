<?php
/**
 * Tests for the email field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Email_Field;

/**
 * Class EmailFieldTestCase
 *
 * @group fields
 */
class EmailFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Email_Field( 'CMB_Email_Field', 'Field', [] );
	}

	function testSaveValue() {
		$value = array( 'hm@hmn.md' );
		$field = new CMB_Email_Field( 'foo', 'Foo', $value );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( self::$post->ID, $value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( self::$post->ID, 'foo', false ), $value );
	}

	function testFieldOutput() {
		$field        = new CMB_Email_Field( 'foo', 'Foo', array( 'hm@hmn.md' ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/(type=\"email\".*?id=\"foo-cmb-field-0\".*?value=\"hm@hmn.md\")/s' );

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
			[ [ 'A string' ] ],
			[ [ 'hm@md.com' ] ],
			[ [ 'mike@mike.co.uk' ] ],
		];
	}
}
