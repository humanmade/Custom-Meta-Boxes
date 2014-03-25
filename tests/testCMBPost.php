<?php

class CMBTests extends WP_UnitTestCase {

	private $users = array();
	// private $post_id;

	function setUp() {

		parent::setUp();

		$this->users['subscriber'] = wp_insert_user( array(
			'user_login'  => 'subscriber',
			'user_pass'   => 'subscriber',
			'user_email'  => 'subscriber@test.com',
			'role'        => 'subscriber',
		) );

		$this->users['author_1'] = wp_insert_user( array(
			'user_login'  => 'author',
			'user_pass'   => 'author',
			'user_email'  => 'author@test.com',
			'role'        => 'author',
		) );

		$this->users['author_2'] = wp_insert_user( array(
			'user_login'  => 'author',
			'user_pass'   => 'author',
			'user_email'  => 'author@test.com',
			'role'        => 'author',
		) );

		// insert a post
		$this->post_id = wp_insert_post(
			array(
				'post_author' => $this->author_id,
				'post_status' => 'publish',
				'post_content' => rand_str(),
				'post_title' => rand_str(),
				'tax_input' => array( 'post_tag' => 'tag1,tag2', 'ctax' => 'cterm1,cterm2' ),
				'post_type' => $post_type,
				'post_author' => $this->users['author_1']
			)
		);

	}

	function tearDown() {

		foreach( $this->users as $user_id ) {
			wp_delete_user( $user_id );
		}

		wp_delete_post( $this->post_id, true );

		unset( $this->users );
		unset( $this->post_id );

		parent::tearDown();
	}

	function testIsBoxDisplayed() {

		// Test capability with object ID 2nd param required, but set automatically.
		$cmb = $this->getMockForAbstractClass(
			'CMB',
			array(
				array(
					'id'              => 'test',
					'title'           => 'test',
					'fields'          => array( array( 'id' => 'field-1',  'name' => 'Text input field', 'type' => 'text' ) ),
					'capability'      => 'edit_post'
				)
			)
		);

		$cmb->_object_id = $this->post_id;

		wp_set_current_user( $this->users['subscriber'] );
		$this->assertFalse( $cmb->is_box_displayed(), 'edit_post capability test, box is shown for subscriber.' );

		wp_set_current_user( $this->users['author_1'] );
		$this->assertTrue( $cmb->is_box_displayed(), 'edit_post capability test, box is not shown for post author.' );

		wp_set_current_user( $this->users['author_2'] );
		$this->assertFalse( $cmb->is_box_displayed(), 'edit_post capability test, box is shown for author user who is not the author of the post.' );

		wp_set_current_user( 1 );
		$this->assertTrue( $cmb->is_box_displayed(), 'edit_post capability test, box is not shown for post admin user.' );

		// Test capability with no 2nd param required
		$cmb->args['capability'] = 'edit_posts';

		wp_set_current_user( $this->users['subscriber'] );
		$this->assertFalse( $cmb->is_box_displayed(), 'edit_posts capability test, box is shown for subscriber.' );

		wp_set_current_user( 1 );
		$this->assertTrue( $cmb->is_box_displayed(), 'edit_posts capability test, box is not shown for post admin user.' );

		// Test capability with 2nd param passed manually.
		$cmb->args['capability'] = 'edit_user';
		$cmb->args['capability_args'] = $this->users['author_1'];

		wp_set_current_user( $this->users['subscriber'] );
		$this->assertFalse( $cmb->is_box_displayed(), 'edit_user capability test, box is shown for subscriber.' );

		wp_set_current_user( 1 );
		$this->assertTrue( $cmb->is_box_displayed(), 'edit_user capability test, box is not shown for post admin user.' );

	}

	function testAddFields() {

		$cmb = $this->getMockForAbstractClass(
			'CMB',
			array(
				array(
					'id'              => 'test',
					'title'           => 'test',
					'fields'          => array( array( 'id' => 'field-1',  'name' => 'Text input field', 'type' => 'text' ) ),
				)
			)
		);

		$cmb->add_fields( $cmb->args['fields'] );

		$this->assertInstanceOf( 'CMB_Text_Field', reset( $cmb->get_fields() ), 'Fields incorrectly added' );

	}

}


// class CMBPostTests extends WP_UnitTestCase {

// 	private $post;

// 	function setUp() {

// 		parent::setUp();

// 		// insert a post
// 		$id = wp_insert_post(
// 			array(
// 				'post_author' => $this->author_id,
// 				'post_status' => 'publish',
// 				'post_content' => rand_str(),
// 				'post_title' => rand_str(),
// 				'tax_input' => array( 'post_tag' => 'tag1,tag2', 'ctax' => 'cterm1,cterm2' ),
// 				'post_type' => $post_type
// 			)
// 		);

// 		// fetch the post
// 		$this->post = get_post( $id );

// 	}

// 	function tearDown() {
// 		unset( $this->post );
// 		wp_delete_post( $this->post_id, true );
// 		parent::tearDown();
// 	}

// 	// function testSaveValuesOnRepeatable() {

// 	// 	$field = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ), array( 'repeatable' => true ) );

// 	// 	if ( ! $this->post )
// 	// 		$this->markTestSkipped( 'Post not found' );

// 	// 	$field->save( $this->post->ID, array( 1, 2 ) );

// 	// 	$meta = get_post_meta( $this->post->ID, 'foo', false );

// 	// 	$this->assertEquals( $meta, array( 1, 2 ) );

// 	// }

// }

