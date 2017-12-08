<?php
/**
 * Tests for the group field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Group_Field;
use CMB_Text_Field;

/**
 * Class TestGroupField
 *
 * @group fields
 */
class TestGroupField extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Group_Field( 'CMB_Group_Field', 'Field', [] );
	}

	/**
	 * Test that a fields are correctly added to a group field.
	 */
	function testAddField() {
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array( 2, 3 ), array( 'repeatable' => true ) );

		$group->add_field( $field1 );
		$group->add_field( $field2 );

		$this->assertArrayHasKey( 'foo', $group->get_fields() );
		$this->assertArrayHasKey( 'bar', $group->get_fields() );
	}

	/**
	 * Test that value retrieval within a group field works correctly.
	 */
	function testGetValues() {
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array() );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array() );

		$group->add_field( $field1 );
		$group->add_field( $field2 );

		$group->values = $values = array(
			'group' => array(
				'foo' => array( 1, 2 ),
				'bar' => array( 3, 4 ),
			),
		);

		$this->assertEquals( $group->get_values(), $values );
	}

	/**
	 * Test that vsaving values within a group field works correctly.
	 */
	function testParseSaveValues() {
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1 ) );
		$field2 = new CMB_Text_Field( 'bar', 'Title', array( 2, 3 ), array( 'repeatable' => true ) );

		$group->add_field( $field1 );
		$group->add_field( $field2 );

		$group->set_values( array(
			'group' => array(
				'foo' => array( 1 ),
				'bar' => array( 2, 3 ),
			),
		) );

		$expected = array(
			'group' => array(
				'foo' => 1,
				'bar' => array( 2, 3 ),
			),
		);

		$group->parse_save_values();

		$this->assertEquals( $group->get_values(), $expected );
	}

	/**
	 * Test that the name attribute works correctly.
	 */
	function testFieldNameAttrValue() {
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );

		$group->add_field( $field1 );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-0][foo][cmb-field-0]' );

		// Using append
		$id_attr = $field1->get_the_name_attr( '[bar]' );
		$this->assertEquals( $id_attr, 'group[cmb-group-0][foo][cmb-field-0][bar]' );

		// Test repeatable group.
		$group->field_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-1][foo][cmb-field-0]' );
		$group->field_index = 0; // Unset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-0][foo][cmb-field-1]' );
		$field1->field_index = 0; // Unset

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$group->field_index = 12;
		$id_attr = $field1->get_the_name_attr();
		$this->assertEquals( $id_attr, 'group[cmb-group-12][foo][cmb-field-0]' );
		$group->field_index = 0; // Unset
	}

	/**
	 * Test that the ID attribute works correctly.
	 */
	function testFieldIdAttrValue() {
		$group  = new CMB_Group_Field( 'group', 'Group Title', array() );
		$field1 = new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) );

		$group->add_field( $field1 );

		// Standard use of ID attribute
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-0-foo-cmb-field-0' );

		// Using append
		$id_attr = $field1->get_the_id_attr( 'bar' );
		$this->assertEquals( $id_attr, 'group-cmb-group-0-foo-cmb-field-0-bar' );

		// Test repeatable group.
		$group->field_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-1-foo-cmb-field-0' );
		$group->field_index = 0; // Unset

		// Test repeatable field within group.
		$field1->field_index = 1;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-0-foo-cmb-field-1' );
		$field1->field_index = 0; // Unset

		// Test more than 10 fields
		// See https://github.com/humanmade/Custom-Meta-Boxes/pull/164
		$group->field_index = 12;
		$id_attr = $field1->get_the_id_attr();
		$this->assertEquals( $id_attr, 'group-cmb-group-12-foo-cmb-field-0' );
		$group->field_index = 0; // Unset
	}

	/**
	 * Verify that the field saves values correctly to meta.
	 *
	 * We need to override this method because inner fields must be setup
	 * in order for saving to work correctly.
	 *
	 * @dataProvider valuesProvider
	 *
	 * @param mixed $value          Value to save
	 * @param mixed $expected_value Optional. Expected value to save.
	 */
	function test_save_value( $value, $expected_value = false ) {
		$this->instance->add_field( new CMB_Text_Field( 'foo', 'Title', array( 1, 2 ) ) );

		$this->instance->save( self::$post->ID, $value );

		// Usually, we only want to pass one value and not a parsed value. Accomodate this here.
		if ( false === $expected_value ) {
			$expected_value = $value;
		}

		// Verify single value is properly saved.
		$this->assertEquals(
			$expected_value,
			get_post_meta( self::$post->ID, get_class( $this->instance ), false )
		);
	}

	/**
	 * Update our default argument set with specific args.
	 *
	 * @return array
	 */
	public function argumentsProvider() {
		$args = [
			[
				'fields' => [
					[
						'id' => 'gac-4-f-1',
						'name' => 'Text input field',
						'type' => 'text',
					],
					[
						'id' => 'gac-4-f-2',
						'name' => 'Text input field',
						'type' => 'text',
					],
				],
			],
		];

		return array_merge( $args, parent::argumentsProvider() );
	}

	/**
	 * Provide a default set of values to test saving against.
	 *
	 * P.S. the data structure for this field is NUTS.
	 *
	 * @return array Values set.
	 */
	public function valuesProvider() {
		return [
			[
				[ [ 'foo' => [ 'A string' ] ] ],
				[ [ 'foo' => 'A string' ] ],
			],
			[
				[ [ 'foo' => [ 162735 ] ] ],
				[ [ 'foo' => 162735 ] ],
			],
			[
				[ [ 'foo' => [ true ] ] ],
				[ [ 'foo' => true ] ],
			],
		];
	}
}
