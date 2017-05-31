<?php
/**
 * Tests for the number field type.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class NumberFieldTestCase extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$args = array(
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => rand_str(),
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
		$value = array( 0.5 );
		$field = new CMB_Number_Field( 'foo', 'Foo', $value, array( 'min' => 0, 'max' => 1 ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$field->save( $this->post->ID, $value );

		// Verify single value is properly saved.
		$this->assertEquals( get_post_meta( $this->post->ID, 'foo', false ), $value );
	}

	function testFieldOutput() {
		$field        = new CMB_Number_Field( 'foo', 'Foo', array( 0.5 ), array( 'min' => 0.5, 'max' => 1 ) );

		if ( ! $this->post ) {
			$this->markTestSkipped( 'Post not found' );
		}

		$this->expectOutputRegex( '/(type=\"number\".*?id=\"foo-cmb-field-0\".*?value=\"0.5\")/s' );
		$this->expectOutputRegex( '/min="0.5"/s' );
		$this->expectOutputRegex( '/max="1"/s' );

		// Trigger output.
		$field->html();
	}
}
