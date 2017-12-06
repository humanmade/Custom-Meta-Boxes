<?php

namespace HMCMB\Tests;

use CMB_Checkbox_Multi;

/**
 * Class CheckboxMultiFieldTestCase
 *
 * @group fields
 */
class CheckboxMultiFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Checkbox_Multi( 'CMB_Checkbox_Multi', 'Field', [] );;
	}

	public function testEmptyFieldOutput() {
		$field = new CMB_Checkbox_Multi( 'foo', 'Foo', array( 'value' => 'value' ), array( 'options' => array( 'value' => 'value' ) ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/(type=\"checkbox\".*?id=\"foo-cmb-field-0-item-value\".*?checked=\'checked\')/s' );

		// Trigger output.
		$field->html();
	}

	public function testSavedFieldOutput() {
		$field = new CMB_Checkbox_Multi( 'foo', 'Foo', array( 'value' => 'value' ), array( 'options' => array( 'value' => 'value', 'value2' => 'value2' ) ) );

		if ( ! self::$post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( self::$post->ID, array( 'value2' => 'on' ) );

		$this->expectOutputRegex( '/(type=\"checkbox\".*?id=\"foo-cmb-field-0-item-value2\".*?checked=\'checked\')/s' );

		// Trigger output.
		$field->html();
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'options' => [ 'Option 1', 'Option 2', 'Option 3' ],
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}
}
