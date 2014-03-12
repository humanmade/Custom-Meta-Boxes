<?php

class CMB_User extends CMB {

	public function __construct( $meta_box ) {

		parent::__construct( $meta_box );

		add_action( 'admin_init', array( &$this, 'init_hook' ), 100 );

		add_action( 'show_user_profile', array( &$this, 'display_hook' ) );
		add_action( 'edit_user_profile', array( &$this, 'display_hook' ) );

		add_action( 'personal_options_update',  array( &$this, 'save_hook' ) );
		add_action( 'edit_user_profile_update',  array( &$this, 'save_hook' ) );

	}

	public function init_hook() {

		global $post, $pagenow;

		if ( $pagenow === 'profile.php' )
			$object_id = get_current_user_id();

		// When updating the page, user_id is passed as POST, when loading user_id is passed as GET
		elseif ( $pagenow === 'user-edit.php' && isset( $_REQUEST['user_id'] ) )
			$object_id = $_REQUEST['user_id'];

		if ( empty( $object_id ) )
			return false;

		$this->init( $object_id );

	}

	public function save_hook( $object_id ) {
		$this->save( $object_id );
	}

	function display_hook( $object ) {
		printf( '<h3>%s</h3>', esc_html( $this->_meta_box['title'] ) );
		$this->display( $object->ID );
	}

	public function get_field_values( $object_id, $field_id ) {
		return get_user_meta( $object_id, $field_id, false );
	}

	public function save_field_values( $object_id, $field_id, $values ) {

		delete_user_meta( $object_id, $field_id );

		if ( empty( $values ) )
			return;

		foreach ( $values as $value ) {

			if ( $value || $value === '0' )
				update_user_meta( $object_id, $field_id, $value );

		}

	}

}