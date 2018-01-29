<?php
/**
 * Post Select field.
 *
 * @supports "data_delegate"
 * @args
 *     'options'     => array Array of options to show in the select, optionally use data_delegate instead
 *     'allow_none'   => bool Allow no option to be selected (will place a "None" at the top of the select)
 *     'multiple'     => bool whether multiple can be selected
 *
 * @since 1.0.0
 *
 * @extends CMB_Select
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Post_Select extends CMB_Select {

	/**
	 * CMB_Post_Select constructor.
	 */
	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

	}

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'query'    => array(),
				'use_ajax' => false,
				'multiple' => false,
			)
		);
	}

	/**
	 * Get posts and verify for use in select field.
	 *
	 * @todo:: validate this data before returning.
	 *
	 * @return array Array of posts for field.
	 */
	public function get_delegate_data() {

		if ( $this->args['use_ajax'] ) {
			$posts = (array) $this->get_value();
		} else {
			$posts = $this->get_posts();
		}

		$data = array();
		foreach ( $posts as $post_id ) {
			$data[ $post_id ] = get_the_title( $post_id );
		}

		return $data;

	}

	/**
	 * Get posts for use in select field.
	 *
	 * @return array
	 */
	private function get_posts() {

		$this->args['query']['fields'] = 'ids';
		$query = new WP_Query( $this->args['query'] );

		return isset( $query->posts ) ? $query->posts : array();

	}

	/**
	 * Format the field values for Select2 to read.
	 */
	public function parse_save_value() {

		// AJAX multi select2 data is submitted as a string of comma separated post IDs.
		// If empty, set to false instead of empty array to ensure the meta entry is deleted.
		if ( $this->args['use_ajax'] && $this->args['multiple'] ) {
			$this->value = ( ! empty( $this->value ) ) ? explode( ',', $this->value ) : false;
		}

	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		$this->args['query']['fields'] = 'ids';

		parent::enqueue_scripts();

	}

	/**
	 * Output inline scripts to support field.
	 */
	public function output_script() {

		$this->field_data['ajax_data'] = array(
			'action'  => 'cmb_post_select',
			'post_id' => intval( get_the_id() ),
			'nonce'   => wp_create_nonce( 'cmb_select_field' ),
			'query'   => $this->args['query'],
		);
		$this->field_data['ajax_url'] = esc_url( admin_url( 'admin-ajax.php' ) );

		parent::output_script();
	}
}
