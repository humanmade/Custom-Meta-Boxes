<?php

class CmbMetaboxClassTests extends WP_UnitTestCase {

	/**
	 * Holds the test post ID.
	 *
	 * @var int
	 */
	private $post_id = 0;

	/**
	 * Holds a simlpe fieldset.
	 *
	 * @var array
	 */
	private $simple_fields = [];

	/**
	 * Holds a simlpe fieldset.
	 *
	 * @var array
	 */
	private $group_fields = [];

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

		$this->simple_fields = array(
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

		$this->group_fields = array(
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

	}

	/**
	 * Clean up after a test.
	 */
	function tearDown() {
		wp_delete_post( $this->post_id );
		parent::tearDown();
	}


	function test_values_from_init_non_repeatable() {

		$values = array( 1, 2, 3, 4, 5 );

		$field = $this->intialize_fields( $this->simple_fields, $values );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( 1 ), $field->values );

		unset( $boxes );
	}

	function test_values_from_init_repeatable() {

		// Change simple field to be a repeatable.
		$this->simple_fields['fields'][0]['repeatable'] = true;

		$values = array( 1, 2, 3, 4, 5 );

		$field = $this->intialize_fields( $this->simple_fields, $values );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( 1, 2, 3, 4, 5 ), $field->values );

		unset( $boxes );

	}

	function test_values_from_init_group_non_repeatable() {

		$values = array( array(
			'text'  => array( 'one', 'two', 'three' ),
			'text2' => array( 'four', 'five', 'six' ),
		) );

		$field = $this->intialize_fields( $this->group_fields, $values );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals( array( array( 'text' => 'one', 'text2' => 'four' ) ), $field->values );

		unset( $boxes );

	}

	function test_values_from_init_group_repeatable() {

		// Change fields to be a repeatable.
		$this->group_fields['fields'][0]['fields'][0]['repeatable'] = true;
		$this->group_fields['fields'][0]['fields'][1]['repeatable'] = true;

		$values = array( array(
			'text'  => array( 'one', 'two', 'three' ),
			'text2' => array( 'four', 'five', 'six' ),
		) );

		$field = $this->intialize_fields( $this->group_fields, $values );

		// Check that a non-repeatable field only returns one value.
		// The new instance of the field is 1.
		$this->assertEquals(
			array(
				array(
					'text' => array( 'one', 'two', 'three' ),
					'text2' => array( 'four', 'five', 'six' ),
				)
			),
			$field->values
		);

		unset( $boxes );

	}

	/**
	 * Mock up a CMB_MetaBox instance, save a field value and the return the boxes for testing
	 *
	 * @param $fields
	 * @param $values
	 * @return object Field type class instance.
	 */
	private function intialize_fields( $fields, $values ) {
		$boxes = new CMB_Meta_Box( $fields );
		$boxes->init_fields( $this->post_id );

		// Save some values for testing.
		$boxes->fields[0]->save( $this->post_id, $values );

		// Re-initialize the boxes and check the data.
		$boxes->init_fields( $this->post_id );

		return $boxes->fields[1];
	}
}