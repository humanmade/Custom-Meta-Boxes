<?php
/**
 * Date picker meta box field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Date_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		// If the user has set a cols arg of less than 6 columns, allow the intput
		// to go full-width.
		$classes = ( is_int( $this->args['cols'] ) && $this->args['cols'] <= 6 ) ? 'cmb_datepicker' : 'cmb_text_small cmb_datepicker' ;
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( $classes ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value ); ?>" />

		<?php
	}
}
