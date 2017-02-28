<?php
/**
 * Field for image upload / file upload.
 *
 * @todo ability to set image size (preview image) from caller
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_File_Field extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'library-type' => array(
					'video',
					'audio',
					'text',
					'application',
				),
			)
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	function enqueue_scripts() {

		global $post_ID;
		$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;

		parent::enqueue_scripts();

		wp_enqueue_media( array( 'post' => $post_ID ) );
		wp_enqueue_script( 'cmb-file-upload', trailingslashit( CMB_URL ) . 'js/file-upload.js', array( 'jquery', 'cmb-scripts' ), CMB_VERSION );

	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->get_value() ) {
			$src = wp_mime_type_icon( $this->get_value() );
			if ( strpos( $src, site_url() !== false ) ) {
				$size = getimagesize( str_replace( site_url(), ABSPATH, $src ) );
			} else {
				$size = null;
			}
			$icon_img = '<img src="' . $src . '" ' . ( $size ? $size[3] : '' ) . ' />';
		}

		$data_type = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="cmb-file-wrap" <?php echo 'data-type="' . esc_attr( $data_type ) . '"'; ?>>

			<div class="cmb-file-wrap-placeholder"></div>

			<button class="button cmb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>">
				<?php esc_html_e( 'Add File', 'cmb' ); ?>
			</button>

			<div class="cmb-file-holder type-file <?php echo $this->get_value() ? '' : 'hidden'; ?>">

				<?php if ( $this->get_value() ) : ?>

					<?php
					if ( isset( $icon_img ) ) {
						echo $icon_img;
					}
					?>

					<div class="cmb-file-name">
						<strong><?php echo esc_html( basename( get_attached_file( $this->get_value() ) ) ); ?></strong>
					</div>

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

		<?php
	}
}
