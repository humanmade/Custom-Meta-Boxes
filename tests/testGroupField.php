<?php

class GroupFieldTestCase extends WP_UnitTestCase {

	function testAddField() {

		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array( 3, 4 ) );

		$group->add_field( $field1 );
		$group->add_field( $field2 );
	
		$this->assertArrayHasKey( 'foo', $group->fields );
		$this->assertArrayHasKey( 'bar', $group->fields );
	
	}

	function testGetValues() {
		
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array() );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array() );
	
		$group->add_field( $field1 );
		$group->add_field( $field2 );
		
		$group->values = $values = array( 
			'group' => array( 
				'foo' => array( 1, 2 ),
				'bar' => array( 3, 4 ) 
			)
		);

		$this->assertEquals( $group->get_values(), $values );
	
	}

	function testParseSaveValues() {

		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array( 3, 4 ) );
	
		$group->add_field( $field1 );
		$group->add_field( $field2 );
		
		$group->set_values( $values = array( 
			'group' => array( 
				'foo' => array( 1, 2 ),
				'bar' => array( 3, 4 ) 
			),
		) );

		$group->parse_save_values();

		$this->assertEquals( $group->get_values(), $values );

	}

	function testFieldNameAttrValue() {

		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );
	
		$group->add_field( $field1 );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[foo][cmb-group-0][cmb-field-0]' );

		// Using append
		$id_attr = $field1->get_the_name_attr( '[bar]' );
		$this->assertEquals( $id_attr, 'group[foo][cmb-group-0][cmb-field-0][bar]' );

		// Test repeatable group.
		$field1->group_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[foo][cmb-group-1][cmb-field-0]' );
		$field1->group_index = 0; // reset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[foo][cmb-group-0][cmb-field-1]' );
		$field1->field_index = 0; // Unset

	}

	function testFieldIdAttrValue() {

		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );
	
		$group->add_field( $field1 );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-foo-cmb-group-0-cmb-field-0' );

		// Using append
		$id_attr = $field1->get_the_id_attr( 'bar' );
		$this->assertEquals( $id_attr, 'group-foo-cmb-group-0-cmb-field-0-bar' );

		// Test repeatable group.
		$field1->group_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-foo-cmb-group-1-cmb-field-0' );
		$field1->group_index = 0; // reset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-foo-cmb-group-0-cmb-field-1' );
		$field1->field_index = 0; // Unset

	}

}