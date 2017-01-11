<?php

class FieldTestCase extends WP_UnitTestCase {

	private $post;

	function setUp() {

		parent::setUp();

		$args = array(
			'post_author' => 1,
			'post_status' => 'publish',
			'post_content' => rand_str(),
			'post_title' => rand_str(),
			'post_type' => 'post',
		);

		$id = wp_insert_post( $args );

		$this->post = get_post( $id );

	}

	function tearDown() {
		wp_delete_post( $this->post->ID, true );
		unset( $this->post );
		parent::tearDown();
	}

	function testGetValues() {

		// Single Value
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );
		$this->assertEquals( $field->get_values(), array( 1 ) );

		// Single, saved value.
		$field_value  = array( 'one' );
		$field->save( $this->post->ID, $field_value );
		$this->assertEquals( $field->get_values(), $field_value );

		// Multiple Values - eg repeatable.
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ), array( 'repeatable' => true ) );
		$this->assertEquals( $field->get_values(), array( 1, 2 ) );

		// Multiple, saved values.
		$repeat_value = array( 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'zero' );
		$field->save( $this->post->ID, $repeat_value );
		$this->assertEquals( $field->get_values(), $repeat_value );

	}

	function testSaveValues() {

		$field        = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );
		$field_value  = array( 'one' );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, $field_value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( $this->post->ID, 'foo', false ), $field_value );

	}

	function testSaveValuesOnRepeatable() {

		$field        = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ), array( 'repeatable' => true ) );
		$repeat_value = array( 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'zero' );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, $repeat_value );

		// Test that the repeatable field is saved properly.
		$this->assertEquals( get_post_meta( $this->post->ID, 'foo', false ), $repeat_value );

	}

	function testIdAttrValue() {

		$field = new CMB_Text_Field( 'foo', 'Text', array( 1, 2 ) );

		// Standard use of ID attribute
		$id_attr = $field->get_the_id_attr();
		$this->assertEquals( $id_attr, 'foo-cmb-field-0' );

		// Using append
		$id_attr = $field->get_the_id_attr( 'bar' );
		$this->assertEquals( $id_attr, 'foo-cmb-field-0-bar' );

		// Repeatable
		$field->field_index = 1;
		$id_attr = $field->get_the_id_attr();
		$this->assertEquals( $id_attr, 'foo-cmb-field-1' );

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$field->field_index = 12;
		$id_attr = $field->get_the_id_attr();
		$this->assertEquals( $id_attr, 'foo-cmb-field-12' );

	}

	function testNameAttrValue() {

		$field = new CMB_Text_Field( 'foo', 'Text', array( 1, 2 ) );

		// Standard use of ID attribute
		$id_attr = $field->get_the_name_attr();
		$this->assertEquals( $id_attr, 'foo[cmb-field-0]' );

		// Using append
		$id_attr = $field->get_the_name_attr( '[bar]' );
		$this->assertEquals( $id_attr, 'foo[cmb-field-0][bar]' );

		// Repeatable
		$field->field_index = 1;
		$id_attr = $field->get_the_name_attr();
		$this->assertEquals( $id_attr, 'foo[cmb-field-1]' );

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$field->field_index = 12;
		$id_attr = $field->get_the_name_attr();
		$this->assertEquals( $id_attr, 'foo[cmb-field-12]' );

	}

	function testEmptyFieldOutput() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		// Test empty output
		$this->expectOutputRegex( '/(type=\"text\".*?id=\"foo-cmb-field-0\".*?value=\"1\")/s' );

		// Trigger output.
		$field->html();

	}

	function testSavedFieldOutput() {
		$field        = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );
		$field_value  = array( 'one' );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, $field_value );

		$this->expectOutputRegex( '/(type=\"text\".*?id=\"foo-cmb-field-0\".*?value=\"one\")/s' );

		// Trigger output.
		$field->html();
	}

}
