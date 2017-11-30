<?php

class FieldTestCase extends WP_UnitTestCase {

	private $post;

	private $users = array();

	function setUp() {
		parent::setUp();

		// Setup some users to test our display logic.
		$this->users['admin'] = $this->factory->user->create( array( 'role' => 'administrator' ) );
		$this->users['author'] = $this->factory->user->create( array( 'role' => 'author' ) );

		// Setup a post for testing is_displayed.
		$this->post = $this->factory->post->create_and_get( array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => rand_str(),
			'post_type'    => 'post',
		) );
	}

	function tearDown() {
		wp_delete_post( $this->post->ID, true );
		unset( $this->post );
		wp_set_current_user( 0 );
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

	function testDisplayedRepeatableButtonDiv() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ), array( 'repeatable' => true ) );

		// Set expectation for an empty output.
		$this->expectOutputRegex( '/\<div class=\"field-item hidden\" .*?\>/' );

		hmcmb_invoke_method( $field, 'repeatable_button_markup' );
	}

	function testDisplayedRepeatableButtonButton() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ), array( 'repeatable' => true ) );

		// Set expectation for an empty output.
		$this->expectOutputRegex( '/\<button class=\"button repeat-field\"\>Add New<\/button\>/' );

		hmcmb_invoke_method( $field, 'repeatable_button_markup' );
	}

	function testDisplayedDeleteButton() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ), array( 'repeatable' => true ) );

		// Set expectation for an empty output.
		$this->expectOutputRegex( '/\<button class=\"cmb-delete-field\" title=\"Remove\">/' );

		hmcmb_invoke_method( $field, 'delete_button_markup' );
	}

	/**
	 * A single empty field should  display upon load.
	 */
	function testEmptyFieldDisplay() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		// Test empty output
		$this->expectOutputRegex( '/(div class\=\"field-title\"\>.*?type=\"text\".*?id=\"foo-cmb-field-0\".*?value=\"1\")/s' );

		// Trigger output.
		$field->display();
	}

	/**
	 * A single empty field should still display on repeatable fields upon load.
	 */
	function testEmptyFieldDisplayRepeatable() {
		$field = new CMB_Text_Field( 'foo', 'Title', array(), array( 'repeatable' => true ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		// Test empty output
		$this->expectOutputRegex( '/(div class\=\"field-title\"\>.*?type=\"text\".*?id=\"foo-cmb-field-0\".*?value=\"\")/s' );

		// Trigger output.
		$field->display();
	}

	function testIsDisplayed() {
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );

		wp_set_current_user( $this->users['admin'] );

		// Test default value against default admin.
		$this->assertTrue( $field->is_displayed( $this->post->ID ) );

		// Re-setup the field with some capability logic in there.
		$field = new CMB_Text_Field( 'foo', 'Title', array( 1 ), array( 'capability' => 'edit_others_posts' ) );

		// Should still return true for admin.
		$this->assertTrue( $field->is_displayed( $this->post->ID ) );

		// Change to the author and test against our modified permission.
		wp_set_current_user( $this->users['author'] );

		$this->assertFalse( $field->is_displayed( $this->post->ID ) );
	}
}
