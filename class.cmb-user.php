<?php

class CMB_User extends CMB {

	public function __construct( $meta_box ) {

		parent::__construct( $meta_box );

		add_action( 'current_screen', array( &$this, 'init_hook' ), 100 );
		
		add_action( 'show_user_profile', array( &$this, 'display_hook' ) );
		add_action( 'edit_user_profile', array( &$this, 'display_hook' ) );
		
		add_action( 'personal_options_update',  array( &$this, 'save_hook' ) );
		add_action( 'edit_user_profile_update',  array( &$this, 'save_hook' ) );

	}

	public function init_hook() {

		global $post;
		
		$screen = get_current_screen();

		if ( $screen->id === 'profile' )
			$object_id = get_current_user_id();

		elseif ( $screen->id === 'user-edit' && isset( $_GET['user_id'] ) )
			$object_id = $_GET['user_id'];

		if ( ! isset( $object_id ) )
			return false;

		$this->init( $object_id ); 

	}

	public function save_hook( $object_id ) {

		$this->save( $object_id ); 

	}

	function display_hook( $object ) {
		$this->display( $object->id ); 
	}

	public function get_field_values( $object_id, $field_id ) {
		
		return get_user_meta( $object_id, $field_id, false );

	}

	public function save( $object_id ) {

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $object_id;

		parent::save( $object_id );

	}

	public function save_field_values( $object_id, $field_id, $values ) {
		
		delete_user_meta( $object_id, $field_id );
		
		if ( empty( $values ) )
			return;

		foreach ( $values as $value ) {
		
			if ( $value || $value === '0' )
				add_user_meta( $object_id, $field_id, $value );

		}

	}

}