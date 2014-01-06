<?php

class CMB_Group extends CMB {

	public $values = array();
	public $parent;

	public function init( $object_id ) {
		
		parent::init( $object_id );

		$fields = &$this->get_fields();
		foreach ( $fields as &$field ) 
			$field->parent = $this->parent;
	
	}

	public function set_parent( $parent ) {
		$this->parent = $parent;
	}

	public function set_values( $values ) {
		$this->values = $values;
	}

	public function get_field_values( $object_id, $field_id ) {
		
		if ( ! isset( $this->values[$this->parent->field_index] ) )
			return array('');

		return isset( $this->values[$this->parent->field_index][ $field_id ] ) ? $this->values[$this->parent->field_index][ $field_id ] : array('');
	}

	public function save_field_values( $object_id, $field_id, $values ) {}

}
