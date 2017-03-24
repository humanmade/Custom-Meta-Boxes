<?php
/**
 * Taxonomy-specific select field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Select
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Taxonomy extends CMB_Select {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'taxonomy'   => '',
				'hide_empty' => false,
			)
		);
	}

	/**
	 * CMB_Taxonomy constructor.
	 */
	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

	}

	/**
	 * Retrieve custom field data.
	 *
	 * @return array Terms for field data.
	 */
	public function get_delegate_data() {

		$terms = $this->get_terms();

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$term_options = array();

		foreach ( $terms as $term ) {
			$term_options[ $term->term_id ] = $term->name;
		}

		return $term_options;

	}

	/**
	 * Get terms for select field.
	 *
	 * @todo::cache this or find a cached method
	 *
	 * @return array|int|WP_Error
	 */
	private function get_terms() {

		return get_terms( $this->args['taxonomy'], array( 'hide_empty' => $this->args['hide_empty'] ) );

	}
}
