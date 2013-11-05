<?php

class CMB_Group extends CMB {

	public $values = array();

	public function __construct( $meta_box ) {
		parent::__construct( $meta_box );
	}

	public function set_values( $values ) {
		$this->values = $values;
	}

	public function get_field_values( $object_id, $field_id ) {
		return $this->values[ $field_id ];
	}

	public function save_field_values( $object_id, $field_id, $values ) {
	}

}
