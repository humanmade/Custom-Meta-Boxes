<?php

class IsMetaBoxDisplayedTestCase extends WP_UnitTestCase {

	private $post;

	function setUp() {

		parent::setUp();

		$args = array(
			'post_author' => 1,
			'post_status' => 'publish',
			'post_content' => rand_str(),
			'post_title' => rand_str(),
			'post_type' => 'post'
		);

		$id = wp_insert_post( $args );

		update_post_meta( $id, '_wp_page_template', 'test.php' );

		$this->post = get_post( $id );

	}

	function tearDown() {
		wp_delete_post( $this->post->ID, true );
		unset( $this->post );
		parent::tearDown();
	}

	function testAddForId() {

		$mb = new CMB_Meta_Box( array(
			'title' => 'Test Meta Box',
			'pages' => 'post',
			'fields' => array(
				array(
					'id' => 'test',
					'name' => 'Test',
					'type' => 'text',
				)
			),
			'show_on' => array( 'id' => 2 )
		) );

		$this->assertFalse( $mb->add_for_id( false ) );

		$_GET['post'] = 2;
		$this->assertTrue( $mb->add_for_id( false ) );

		$_GET['post'] = 3;
		$this->assertFalse( $mb->add_for_id( false ) );

		unset( $_GET['post'] );

	}

	function testAddForTemplate() {

		$mb = new CMB_Meta_Box( array(
			'title' => 'Test Meta Box',
			'pages' => 'post',
			'fields' => array(
				array(
					'id' => 'test',
					'name' => 'Test',
					'type' => 'text',
				)
			),
			'show_on' => array( 'page-template' => 'test.php' )
		) );

		$this->assertFalse( $mb->add_for_page_template( true ) );

		$_GET['post'] = $this->post->ID;
		$this->assertTrue( $mb->add_for_page_template( true ) );

		$_GET['post'] = 3;
		$this->assertFalse( $mb->add_for_page_template( true ) );

		unset( $_GET['post'] );

	}

	function testHideForId() {

		$mb = new CMB_Meta_Box( array(
			'title' => 'Test Meta Box',
			'pages' => 'post',
			'fields' => array(
				array(
					'id' => 'test',
					'name' => 'Test',
					'type' => 'text',
				)
			),
			'hide_on' => array( 'id' => 2 )
		) );

		// Test is shown when no ID is set.
		$displayed = $mb->hide_for_id( true );
		$this->assertTrue( $displayed );

		// Test hidden for post ID
		$_GET['post'] = 2;
		$displayed = $mb->hide_for_id( true );
		$this->assertFalse( $displayed );

		// Test not hidden when post ID doesn't match.
		$_GET['post'] = 3;
		$displayed = $mb->hide_for_id( true );
		$this->assertTrue( $displayed );

		unset( $_GET['post'] );

	}

	function testHideForTemplate() {

		$mb = new CMB_Meta_Box( array(
			'title' => 'Test Meta Box',
			'pages' => 'post',
			'fields' => array(
				array(
					'id' => 'test',
					'name' => 'Test',
					'type' => 'text',
				)
			),
			'hide_on' => array( 'page-template' => 'test.php' )
		) );

		$this->assertTrue( $mb->hide_for_page_template( true ) );

		$_GET['post'] = $this->post->ID;
		$this->assertFalse( $mb->hide_for_page_template( true ) );

		$_GET['post'] = 3;
		$this->assertTrue( $mb->hide_for_page_template( true ) );

		unset( $_GET['post'] );

	}

}