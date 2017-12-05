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

		$this->instance = new CMB_Hidden_Field( 'CMB_Hidden_Field', 'Field', [] );
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
