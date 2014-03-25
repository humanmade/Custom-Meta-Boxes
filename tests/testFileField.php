<?php

class FileTestCase extends WP_UnitTestCase {

	private $post;

	function setUp() {

		parent::setUp();

		// insert a post
		$id = wp_insert_post(
			array(
				'post_author' => $this->author_id,
				'post_status' => 'publish',
				'post_content' => rand_str(),
				'post_title' => rand_str(),
				'tax_input' => array( 'post_tag' => 'tag1,tag2', 'ctax' => 'cterm1,cterm2' ),
				'post_type' => $post_type
			)
		);

		// fetch the post
		$this->post = get_post( $id );

	}

	function tearDown() {
		unset( $this->post );
		wp_delete_post( $this->post_id, true );
		parent::tearDown();
	}

	function testEnqueueScripts() {

		// Single Value
		$field = new CMB_File_Field( 'foo', 'Title', array( 1 ) );
		$field->enqueue_scripts();

		$this->assertTrue( wp_script_is( 'cmb-file-upload', 'queue' ), "cmb-file-upload is not enqueued" );

	}



}