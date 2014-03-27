<?php

class CMB_Group extends CMB {

	public $values = array();
	public $parent;

	public function set_parent( $parent ) {
		$this->parent = $parent;
	}

	public function init( $object_id ) {

		parent::init( $object_id );

		$fields = &$this->get_fields();

		foreach ( $fields as &$field ) {
			$field->parent = $this->parent;
		}

	}

	public function set_values( $values ) {
		foreach ( $this->get_fields() as $key => $field ) {
			if ( isset( $values[$field->id] ) ) {
				$field->values = $values[$field->id];
			}
		}
	}

}
