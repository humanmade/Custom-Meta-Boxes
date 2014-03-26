<?php

class CMBPostTests extends WP_UnitTestCase {

	private $post_id;

	function setUp() {

		parent::setUp();

		// insert a post
		$this->post_id = wp_insert_post(
			array(
				'post_author' => $this->author_id,
				'post_status' => 'publish',
				'post_content' => rand_str(),
				'post_title' => rand_str(),
				'tax_input' => array( 'post_tag' => 'tag1,tag2', 'ctax' => 'cterm1,cterm2' ),
				'post_type' => $post_type
			)
		);

	}

	function tearDown() {
		unset( $this->post );
		wp_delete_post( $this->post_id, true );
		parent::tearDown();
	}

	function testGetData() {

		$cmb = new CMB_Post(
			array(
				'id'              => 'test',
				'title'           => 'test',
				'fields'          => array( array( 'id' => 'field-1',  'name' => 'Text input field', 'type' => 'text' ) ),
			)
		);

		add_post_meta( $this->post_id, 'field-1', 'random_value' );
		$this->assertEquals(  array( 'random_value' ), $cmb->get_data( $this->post_id, 'field-1' ) );

		add_post_meta( $this->post_id, 'field-1', 'random_value_2' );
		$this->assertEquals(  array( 'random_value', 'random_value_2' ), $cmb->get_data( $this->post_id, 'field-1' ) );

		delete_post_meta( $this->post_id, 'field-1' );
	}

	function testInit() {

		if ( ! $this->post_id )
			$this->markTestSkipped( 'Post not found' );

		// Test capability with object ID 2nd param required, but set automatically.
		$cmb = new CMB_Post(
			array(
				'id'              => 'test',
				'title'           => 'test',
				'fields'          => array( array( 'id' => 'field-1',  'name' => 'Text input field', 'type' => 'text' ) ),
			)
		);

		// Set up data.
		add_post_meta( $this->post_id, 'field-1', 'random_value' );

		$cmb->init( $this->post_id );

		// Test init fields.
		$fields = $cmb->get_fields();
		$this->assertCount( 1, $fields, 'number of fields is not expected' );
		$this->assertEquals( $fields[0]->get_values(), array( 'random_value' ) );

		// Clean up.
		delete_post_meta( $this->post_id, 'field-1' );

	}

	function testSave() {

		if ( ! $this->post_id )
			$this->markTestSkipped( 'Post not found' );

		// Test capability with object ID 2nd param required, but set automatically.
		$cmb = new CMB_Post(
			array(
				'id'              => 'test',
				'title'           => 'test',
				'fields'          => array( array( 'id' => 'field-1',  'name' => 'Text input field', 'type' => 'text' ) ),
			)
		);

		// Set up data.
		add_post_meta( $this->post_id, 'field-1', 'random_value' );

		$cmb->init( $this->post_id );

		foreach ( $cmb->get_fields() as $field ) {

			$cmb->save_data( $this->post_id, $field->id, array( 'updated_value' ) );

			$this->assertEquals( array('updated_value'), $cmb->get_data( $this->post_id, $field->id ) );

		}

		// Clean up.
		delete_post_meta( $this->post_id, 'field-1' );

	}
}

