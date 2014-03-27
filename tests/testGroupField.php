<?php

class GroupFieldTestCase extends WP_UnitTestCase {

	private $args = array(
		'id' => 'test-group',
		'name' => 'Test Group',
		'type' => 'group',
		'repeatable' => 1,
		'sortable' => 1,
		'fields' =>  array(
			array( 'id' => 'field-1',  'name' => 'Unit Testing 1', 'type' => 'text' ),
			array( 'id' => 'field-2',  'name' => 'Unit Testing 2', 'type' => 'text' ),
		),
		'desc' => 'This is the group description.'
	);

	function testInitFields() {

		// Test values - random strings.
		$values = array(
			'field-1' => array( substr(md5(rand()),0,10) ),
			'field-2' => array( substr(md5(rand()),0,10) )
		);

		$group = new CMB_Group_Field( 'test-group', 'Test Group', $values, $this->args );

		$this->assertInstanceOf( 'CMB_Group', $group->get_group_fields() );

		$fields = $group->get_group_fields()->get_fields();
		$this->assertInstanceOf( 'CMB_Text_Field', reset( $fields ) );



		// die( print_r( $fields[0]->get_values(), true ) );
		// die( print_r( $group->get_values(), true ) );
		// $group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		// // $field1 = new CMB_Text_Field( 'field-1', 'Title', array( 1 ) );
		// // $field2 = new CMB_Text_Field( 'bar', 'Title', array( 2, 3 ), array( 'repeatable' => true ) );

		// $group->add_field( $field1 );
		// $group->add_field( $field2 );

		// $this->assertArrayHasKey( 'field-1', $group->get_fields() );
		// $this->assertArrayHasKey( 'bar', $group->get_fields() );

	}

	// function testGetValues() {

	// 	$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
	// 	$field1 = new CMB_Text_Field( 'field-1', 'Title', array() );
	// 	$field2 = new CMB_Text_Field( 'bar', 'Title', array() );

	// 	$group->add_field( $field1 );
	// 	$group->add_field( $field2 );

	// 	$group->values = $values = array(
	// 		'group' => array(
	// 			'field-1' => array( 1, 2 ),
	// 			'bar' => array( 3, 4 )
	// 		)
	// 	);

	// 	$this->assertEquals( $group->get_values(), $values );

	// }

	function testParseSaveValues() {

		$group = new CMB_Group_Field( 'group', 'Test Group', array( '1', '2' ), $this->args );
		$fields = $group->get_group_fields()->get_fields();
		$field1 = $fields[0];
		$field2 = $fields[1];

		// die( print_r( $field1, true ) );
		// $group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		// $field1 = new CMB_Text_Field( 'field-1', 'Title', array( 1 ) );
		// $field2 = new CMB_Text_Field( 'bar', 'Title', , array( 'repeatable' => true ) );

		// $group->get_group_fields()->set_values( array(
		// 	'group' => array(
		// 		'field-1' => array( 1 ),
		// 		'bar' => array( 2, 3 )
		// 	),
		// ) );

		// $expected = array(
		// 	'group' => array(
		// 		'field-1' => 1,
		// 		'bar' => array( 2, 3 )
		// 	)
		// );

		// $group->parse_save_values();

		// $this->assertEquals( $group->get_values(), $expected );

	}

	function testFieldNameAttrValue() {

		$group = new CMB_Group_Field( 'group', 'Test Group', array(), $this->args );
		$fields = $group->get_group_fields()->get_fields();
		$field1 = reset( $fields );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-0][field-1][cmb-field-0]' );

		// Using append
		$id_attr = $field1->get_the_name_attr( '[bar]' );
		$this->assertEquals( $id_attr, 'group[cmb-group-0][field-1][cmb-field-0][bar]' );

		// Test repeatable group.
		$group->field_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-1][field-1][cmb-field-0]' );
		$group->field_index = 0; // Unset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-0][field-1][cmb-field-1]' );
		$field1->field_index = 0; // Unset

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$group->field_index = 12;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-12][field-1][cmb-field-0]' );
		$group->field_index = 0; // Unset



	}

	function testFieldIdAttrValue() {

		$group = new CMB_Group_Field( 'group', 'Test Group', array(), $this->args );
		$fields = $group->get_group_fields()->get_fields();
		$field1 = reset( $fields );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-0-field-1-cmb-field-0' );

		// Using append
		$id_attr = $field1->get_the_id_attr( 'bar' );
		$this->assertEquals( $id_attr, 'group-cmb-group-0-field-1-cmb-field-0-bar' );

		// Test repeatable group.
		$group->field_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-1-field-1-cmb-field-0' );
		$group->field_index = 0; // Unset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-0-field-1-cmb-field-1' );
		$field1->field_index = 0; // Unset

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$group->field_index = 12;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-12-field-1-cmb-field-0' );
		$group->field_index = 0; // Unset

	}

}