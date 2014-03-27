<?php

class CMB_User extends CMB {

	public function __construct( $args ) {

		parent::__construct( $args );

		add_action( 'admin_init', array( &$this, 'init_hook' ), 100 );

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

	public function setup_hooks() {

		if ( $this->is_displayed() ) {

			add_action( 'show_user_profile', array( &$this, 'display_hook' ) );
			add_action( 'edit_user_profile', array( &$this, 'display_hook' ) );

			add_action( 'personal_options_update',  array( &$this, 'save_hook' ) );
			add_action( 'edit_user_profile_update',  array( &$this, 'save_hook' ) );

		}

		parent::setup_hooks();

	}

	public function is_displayed() {

		global $pagenow;

		if (
			$pagenow === 'user-edit.php' && isset( $_GET['user_id'] ) ||
			$pagenow === 'profile.php'
		) {
			return parent::is_displayed();
		}

		return false;

	}

	public function save_hook( $object_id ) {
		$this->save( $object_id, $_POST );
	}

	function display_hook( $object ) {
		printf( '<h3>%s</h3>', esc_html( $this->args['title'] ) );
		$this->display( $object->ID );
	}

	public function get_data( $object_id, $field_id ) {
		return get_user_meta( $object_id, $field_id, true );
	}

	public function save_data( $object_id, $field_id, $values ) {

		delete_user_meta( $object_id, $field_id );

		if ( empty( $values ) )
			return;

		// User Meta doesn't seem to handle multiple meta rows per key.
		update_user_meta( $object_id, $field_id, $values );

	}

}