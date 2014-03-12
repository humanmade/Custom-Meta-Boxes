<?php

class CMB_Post extends CMB {

	public function __construct( $meta_box ) {

		parent::__construct( $meta_box );

		if ( ! $this->should_show_field() )
			return;

		add_action( 'admin_init', array( &$this, 'init_hook' ) );
		add_action( 'admin_menu', array( &$this, 'add_post_meta_box' ) );
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

		if ( is_page() || ! isset( $object_id ) )
			return false;

		$this->init( $object_id );

	}

	public function save_hook( $object_id ) {

		$this->save( $object_id );

	}

	function display_hook( $object ) {

		$this->display( $object->id );

	}

	function should_show_pages() {

 		$post_id  = isset( $_GET['post'] ) ? $_GET['post'] : null;
 		$meta_box = $this->_meta_box;

		// Add for post type.
 		if ( isset( $meta_box['pages'] ) ) {
	 		$meta_box['pages'] = ! is_array( $meta_box['pages'] ) ? array( $meta_box['pages'] ) : $meta_box['pages'];
	 		if ( ! in_array( get_post_type( $post_id ), $meta_box['pages'] ) )
	 			return false;
 		}

 		// Add for ID
		if ( isset( $meta_box['show_on']['id'] ) ) {
			// If value isn't an array, turn it into one
			$meta_box['show_on']['id'] = ! is_array( $meta_box['show_on']['id'] ) ? array( $meta_box['show_on']['id'] ) : $meta_box['show_on']['id'];
			if ( ! in_array( $post_id, $meta_box['show_on']['id'] ) )
				return false;
		}

		// Get current template
		// Note assignment
		if ( isset( $meta_box['show_on']['page-template'] ) && $current_template = get_post_meta( $post_id, '_wp_page_template', true ) ) {
			// If value isn't an array, turn it into one
			$meta_box['show_on']['page-template'] = ! is_array( $meta_box['show_on']['page-template'] ) ? array( $meta_box['show_on']['page-template'] ) : $meta_box['show_on']['page-template'];
			if ( ! in_array( $current_template, $meta_box['show_on']['page-template'] ) )
				return false;
		}

 		return parent::should_show_pages();

	}

	// Add metabox
	function add_post_meta_box() {

		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'low' : $this->_meta_box['priority'];

		foreach ( (array) $this->_meta_box['pages'] as $page )
			add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'display_hook'), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );

	}

	public function get_field_values( $object_id, $field_id ) {

		return get_post_meta( $object_id, $field_id, false );

	}

	public function save( $object_id ) {

		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $object_id;

		parent::save( $object_id );

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