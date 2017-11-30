<?php

class CheckboxMultiFieldTestCase extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$args = array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => 'My random and unimportant test string',
			'post_title'   => 'Random Post',
			'post_type'    => 'post',
		);

		$id = wp_insert_post( $args );

		$this->post = get_post( $id );
	}

	function tearDown() {
		wp_delete_post( $this->post->ID, true );
		unset( $this->post );
		parent::tearDown();
	}

	function testSaveValue() {
		$value = array( 'value' );
		$field = new CMB_Checkbox_Multi( 'foo', 'Foo', $value );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, $value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( $this->post->ID, 'foo', false ), $value );
	}

	function testSaveValuesOnRepeatable() {

	}

	function testEmptyFieldOutput() {
		$field        = new CMB_Checkbox_Multi( 'foo', 'Foo', array( 'value' => 'value' ), array( 'options' => array( 'value' => 'value' ) ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/(type=\"checkbox\".*?id=\"foo-cmb-field-0-item-value\".*?checked=\'checked\')/s' );

		// Trigger output.
		$field->html();
	}

	function testSavedFieldOutput() {
		$field        = new CMB_Checkbox_Multi( 'foo', 'Foo', array( 'value' => 'value' ), array( 'options' => array( 'value' => 'value', 'value2' => 'value2' ) ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, array( 'value2' => 'on' ) );

		$this->expectOutputRegex( '/(type=\"checkbox\".*?id=\"foo-cmb-field-0-item-value2\".*?checked=\'checked\')/s' );

		// Trigger output.
		$field->html();
	}
}
