<?php
/**
 * Field for image upload.
 *
 * @since 1.0.0
 *
 * @extends CMB_File_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Image_Field extends CMB_File_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'size' => 'thumbnail',
				'library-type' => array(
					'image',
				),
				'show_size' => false,
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->get_value() ) {
			$image = wp_get_attachment_image_src( $this->get_value(), $this->args['size'], true );
		}

		// Convert size arg to array of width, height, crop.
		$size = $this->parse_image_size( $this->args['size'] );

		// Inline styles.
		$styles              = sprintf( 'width: %1$dpx; height: %2$dpx; line-height: %2$dpx', intval( $size['width'] ), intval( $size['height'] ) );
		$placeholder_styles  = sprintf( 'width: %dpx; height: %dpx;', intval( $size['width'] ) - 8, intval( $size['height'] ) - 8 );

		$data_type           = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="cmb-file-wrap" style="<?php echo esc_attr( $styles ); ?>" data-type="<?php echo esc_attr( $data_type ); ?>" data-max-width="<?php echo absint( $size['width'] ); ?>">

			<div class="cmb-file-wrap-placeholder" style="<?php echo esc_attr( $placeholder_styles ); ?>">

				<?php if ( $this->args['show_size'] ) : ?>
					<span class="dimensions">
						<?php printf( '%dpx &times; %dpx', intval( $size['width'] ), intval( $size['height'] ) ); ?>
					</span>
				<?php endif; ?>

			</div>

			<button class="button cmb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>" data-nonce="<?php echo wp_create_nonce( 'cmb-file-upload-nonce' ); ?>">
				<?php esc_html_e( 'Add Image', 'cmb' ); ?>
			</button>

			<div class="cmb-file-holder type-img <?php echo $this->get_value() ? '' : 'hidden'; ?>" data-crop="<?php echo (bool) $size['crop']; ?>">

				<?php if ( ! empty( $image ) ) : ?>
					<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo intval( $image[1] ); ?>" height="<?php echo intval( $image[2] ); ?>" />
				<?php endif; ?>

			</div>

			<button class="cmb-remove-file button <?php echo $this->get_value() ? '' : 'hidden'; ?>">
				<?php esc_html_e( 'Remove', 'cmb' ); ?>
			</button>

			<input type="hidden"
				<?php $this->class_attr( 'cmb-file-upload-input' ); ?>
				<?php $this->name_attr(); ?>
				   value="<?php echo esc_attr( $this->value ); ?>"
			/>

		</div>

	<?php }

	/**
	 * Parse the size argument to get pixel width, pixel height and crop information.
	 *
	 * @param string $size Size of image requested.
	 * @return array width, height, crop
	 */
	private function parse_image_size( $size ) {

		// Handle string for built-in image sizes.
		if ( is_string( $size ) && in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			return array(
				'width'  => get_option( $size . '_size_w' ),
				'height' => get_option( $size . '_size_h' ),
				'crop'   => get_option( $size . '_crop' ),
			);
		}

		// Handle string for additional image sizes.
		global $_wp_additional_image_sizes;
		if ( is_string( $size ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
			return array(
				'width'  => $_wp_additional_image_sizes[ $size ]['width'],
				'height' => $_wp_additional_image_sizes[ $size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $size ]['crop'],
			);
		}

		// Handle default WP size format.
		if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) ) {
			$size = array( 'width' => $size[0], 'height' => $size[1] );
		}

		return wp_parse_args( $size, array(
			'width'  => get_option( 'thumbnail_size_w' ),
			'height' => get_option( 'thumbnail_size_h' ),
			'crop'   => get_option( 'thumbnail_crop' ),
		) );

	}

	/**
	 * Ajax callback for outputing an image src based on post data.
	 *
	 * @return null
	 */
	static function request_image_ajax_callback() {

		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'cmb-file-upload-nonce' ) ) ) {
			return;
		}

		$id = absint( $_POST['id'] );

		$size = array(
			intval( $_POST['width'] ),
			intval( $_POST['height'] ),
			'crop' => (bool) $_POST['crop'],
		);

		$image = wp_get_attachment_image_src( $id, $size );
		echo esc_url( reset( $image ) );

		// This is required to return a proper result.
		die();
	}
}

// @todo:: this has got to go somewhere else.
add_action( 'wp_ajax_cmb_request_image', array( 'CMB_Image_Field', 'request_image_ajax_callback' ) );
