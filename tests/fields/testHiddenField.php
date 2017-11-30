<?php

namespace HMCMB\Tests;

use CMB_Hidden_Field;

/**
 * Class HiddenFieldTestCase
 *
 * @group fields
 */
class HiddenFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Hidden_Field( 'field', 'Field', [] );
	}

	function testSaveValue() {
		$value = array( 'value' );
		$field = new CMB_Hidden_Field( 'foo', 'Foo', $value );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( self::$post->ID, $value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( self::$post->ID, 'foo', false ), $value );
	}

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
