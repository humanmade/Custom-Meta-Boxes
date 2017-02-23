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

		if ( ! $this->args['use_ajax'] ) {

			$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

		}

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

		$data = array();

		foreach ( $this->get_posts() as $post_id ) {
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
	 * Assemble and output of field HTML.
	 */
	public function output_field() {

		// If AJAX, must use input type not standard select.
		if ( $this->args['use_ajax'] ) :

			?>

			<input
				<?php $this->id_attr(); ?>
				<?php printf( 'value="%s" ', esc_attr( implode( ',' , (array) $this->value ) ) ); ?>
				<?php printf( 'name="%s"', esc_attr( $this->get_the_name_attr() ) ); ?>
				<?php printf( 'data-field-id="%s" ', esc_attr( $this->get_js_id() ) ); ?>
				<?php $this->boolean_attr(); ?>
				class="cmb_select"
				style="width: 100%"
			/>

			<?php

		else :

			parent::output_field();

		endif;

	}

	/**
	 * Output inline scripts to support field.
	 */
	public function output_script() {

		parent::output_script();

		?>

		<script type="text/javascript">

			(function($) {

				if ( 'undefined' === typeof( window.cmb_select_fields ) ) {
					return false;
				}

				// Get options for this field so we can modify it.
				var id = <?php echo json_encode( $this->get_js_id() ); ?>;
				var options = window.cmb_select_fields[id];

				<?php if ( $this->args['use_ajax'] && $this->args['multiple'] ) : ?>
				// The multiple setting is required when using ajax (because an input field is used instead of select)
				options.multiple = true;
				<?php endif; ?>

				<?php if ( $this->args['use_ajax'] && ! empty( $this->value ) ) : ?>

				options.initSelection = function( element, callback ) {

					var data = [];

					<?php if ( $this->args['multiple'] ) : ?>

					<?php foreach ( (array) $this->value as $post_id ) : ?>
					data.push( <?php echo json_encode( array( 'id' => $post_id, 'text' => html_entity_decode( get_the_title( $post_id ) ) ) ); ?> );
					<?php endforeach; ?>

					<?php else : ?>

					data = <?php echo json_encode( array( 'id' => $this->value, 'text' => html_entity_decode( get_the_title( $this->get_value() ) ) ) ); ?>;

					<?php endif; ?>

					callback( data );

				};

				<?php endif; ?>

				<?php if ( $this->args['use_ajax'] ) : ?>

				<?php $this->args['query']['fields'] = 'ids'; ?>

				var ajaxData = {
					action  : 'cmb_post_select',
					post_id : '<?php echo intval( get_the_id() ); ?>', // Used for user capabilty check.
					nonce   : <?php echo json_encode( wp_create_nonce( 'cmb_select_field' ) ); ?>,
					query   : <?php echo json_encode( $this->args['query'] ); ?>
				};

				options.ajax = {
					url: <?php echo json_encode( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>,
					type: 'POST',
					dataType: 'json',
					data: function( term, page ) {
						ajaxData.query.s = term;
						ajaxData.query.paged = page;
						return ajaxData;
					},
					results : function( results, page ) {
						var postsPerPage = ajaxData.query.posts_per_page = ( 'posts_per_page' in ajaxData.query ) ? ajaxData.query.posts_per_page : ( 'showposts' in ajaxData.query ) ? ajaxData.query.showposts : 10;
						var isMore = ( page * postsPerPage ) < results.total;
						return { results: results.posts, more: isMore };
					}
				}

				<?php endif; ?>

			})( jQuery );

		</script>

		<?php
	}
}
