<?php

class CMB_Post extends CMB {

	public function __construct( $args ) {

		if ( ! $this->is_box_displayed() ) {
			return;
		}

		parent::__construct( $args );

		add_action( 'admin_init', array( &$this, 'init_hook' ) );
		add_action( 'add_meta_boxes', array( &$this, 'add_post_meta_box' ) );
		add_action( 'save_post',  array( &$this, 'save_hook' ) );

	}

	public function init_hook() {

		global $post;

		// Get the current ID
		if ( isset( $_GET['post'] ) )
			$object_id = $_GET['post'];

		elseif( isset( $_POST['post_ID'] ) )
			$object_id = $_POST['post_ID'];

		elseif ( ! empty( $post->ID ) )
			$object_id = $post->ID;

		elseif ( isset( $_GET['post'] ) )
			$object_id  = $_GET['post'];

		if ( is_page() || ! isset( $object_id ) )
			return false;

		$this->init( $object_id );

	}

	public function save_hook( $object_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		$this->save( $object_id, $_POST );

	}

	function display_hook( $object ) {

		$this->display( $object->id );

	}

	function is_box_displayed() {

 		// Add for post type.
 		if ( isset( $this->args['pages'] ) ) {
	 		$this->args['pages'] = ! is_array( $this->args['pages'] ) ? array( $this->args['pages'] ) : $this->args['pages'];
	 		if ( ! in_array( get_post_type( $this->_object_id ), $this->args['pages'] ) ) {
	 			return false;
	 		}
 		}

 		// Add for ID
		if ( isset( $this->args['show_on']['id'] ) ) {
			// If value isn't an array, turn it into one
			$this->args['show_on']['id'] = ! is_array( $this->args['show_on']['id'] ) ? array( $this->args['show_on']['id'] ) : $this->args['show_on']['id'];
			if ( ! in_array( $this->_object_id, $this->args['show_on']['id'] ) ) {
				return false;
			}
		}

		// Get current template
		// Note assignment
		if ( isset( $this->args['show_on']['page-template'] ) && $current_template = get_post_meta( $this->_object_id, '_wp_page_template', true ) ) {
			// If value isn't an array, turn it into one
			$this->args['show_on']['page-template'] = ! is_array( $this->args['show_on']['page-template'] ) ? array( $this->args['show_on']['page-template'] ) : $this->args['show_on']['page-template'];
			if ( ! in_array( $current_template, $this->args['show_on']['page-template'] ) ) {
				return false;
			}
		}

 		return parent::is_box_displayed();

	}

	// Add metabox
	function add_post_meta_box() {

		$this->args['context'] = empty($this->args['context']) ? 'normal' : $this->args['context'];
		$this->args['priority'] = empty($this->args['priority']) ? 'low' : $this->args['priority'];

		foreach ( (array) $this->args['pages'] as $page )
			add_meta_box( $this->args['id'], $this->args['title'], array(&$this, 'display_hook'), $page, $this->args['context'], $this->args['priority'] );

	}

	public function get_field_values( $object_id, $field_id ) {

		return get_post_meta( $object_id, $field_id, false );

	}

	public function save_field_values( $object_id, $field_id, $values ) {

		delete_post_meta( $object_id, $field_id );

		if ( empty( $values ) )
			return;

		foreach ( $values as $value ) {

			if ( $value || $value === '0' )
				add_post_meta( $object_id, $field_id, $value );

		}

	}

}