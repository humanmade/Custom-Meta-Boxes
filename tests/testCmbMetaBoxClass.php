<?php

class CmbMetaboxClassTests extends WP_UnitTestCase {

	/**
	 * Holds the test post ID.
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Initialization.
	 */
	function setUp() {
		parent::setUp();

		// Create a test post.
		$this->post_id = self::factory()->post->create(
			array(
				'post_author'  => get_current_user_id(),
				'post_title'   => 'My test post',
				'post_type'    => 'post',
				'post_status'  => 'publish',
			)
		);

	}

	/**
	 * Clean up after a test.
	 */
	function tearDown() {
		wp_delete_post( $this->post_id );
		parent::tearDown();
	}


	function test_values_from_init_non_repeatable() {

		$simple_fields = array(
			'title' => 'Simple',
			'pages' => 'post',
			'fields' => array(
				array(
					'id'         => 'text',
					'name'       => 'Simple',
					'type'       => 'text',
					'repeatable' => false,
				),
			)
		);

		$boxes = new CMB_Meta_Box( $simple_fields );
		$boxes->init_fields( $this->post_id );

		// Save some values for testing.
		$boxes->fields[0]->save( $this->post_id, array( 1, 2, 3, 4, 5 ) );

		// Re-initialize the boxes and check the data.
		$boxes->init_fields( $this->post_id );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( 1 ), $boxes->fields[1]->values );
	}

	function test_values_from_init_repeatable() {

		$simple_fields = array(
			'title' => 'Simple',
			'pages' => 'post',
			'fields' => array(
				array(
					'id'         => 'text',
					'name'       => 'Simple',
					'type'       => 'text',
					'repeatable' => true,
				),
			)
		);

		$boxes = new CMB_Meta_Box( $simple_fields );
		$boxes->init_fields( $this->post_id );

		// Save some values for testing.
		$boxes->fields[0]->save( $this->post_id, array( 1, 2, 3, 4, 5 ) );

		// Re-initialize the boxes and check the data.
		$boxes->init_fields( $this->post_id );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( 1, 2, 3, 4, 5 ), $boxes->fields[1]->values );

	}

	function test_values_from_init_group_non_repeatable() {

		$group_fields = array(
			'title' => 'Simple',
			'pages' => 'post',
			'fields' => array(
				array(
					'id'         => 'text',
					'name'       => 'Simple',
					'type'       => 'group',
					'fields'     => array(
						array(
							'id'         => 'text',
							'name'       => 'Simple',
							'type'       => 'text',
						),
						array(
							'id'         => 'text2',
							'name'       => 'Simple 2',
							'type'       => 'text',
						),
					),
				),
			)
		);

		$boxes = new CMB_Meta_Box( $group_fields );
		$boxes->init_fields( $this->post_id );

		// Save some values for testing.
		$boxes->fields[0]->save( $this->post_id, array(
			array(
				'text'  => array( 'one', 'two', 'three' ),
				'text2' => array( 'four', 'five', 'six' ),
			)
		) );

		// Re-initialize the boxes and check the data.
		$boxes->init_fields( $this->post_id );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( array( 'text' => 'one', 'text2' => 'four' ) ), $boxes->fields[1]->values );

	}

	function test_values_from_init_group_repeatable() {

		$group_fields = array(
			'title' => 'Simple',
			'pages' => 'post',
			'fields' => array(
				array(
					'id'         => 'text',
					'name'       => 'Simple',
					'type'       => 'group',
					'fields'     => array(
						array(
							'id'         => 'text',
							'name'       => 'Simple',
							'type'       => 'text',
							'repeatable' => true,
						),
						array(
							'id'         => 'text2',
							'name'       => 'Simple 2',
							'type'       => 'text',
							'repeatable' => true,
						),
					),
				),
			)
		);

		$boxes = new CMB_Meta_Box( $group_fields );
		$boxes->init_fields( $this->post_id );

		// Save some values for testing.
		$boxes->fields[0]->save( $this->post_id, array(
			array(
				'text'  => array( 'one', 'two', 'three' ),
				'text2' => array( 'four', 'five', 'six' ),
			)
		) );

		// Re-initialize the boxes and check the data.
		$boxes->init_fields( $this->post_id );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( array( 'text' => array( 'one', 'two', 'three' ), 'text2' => array( 'four', 'five', 'six' ) ) ), $boxes->fields[1]->values );

	}
}