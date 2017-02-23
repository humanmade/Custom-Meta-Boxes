<?php
/**
 *  Colour picker
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Color_Picker extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-colorpicker', trailingslashit( CMB_URL ) . 'js/field.colorpicker.js', array( 'jquery', 'wp-color-picker', 'cmb-scripts' ), CMB_VERSION );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_colorpicker cmb_text_small' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}
