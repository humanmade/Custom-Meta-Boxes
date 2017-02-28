<?php
/**
 * Date picker for date only (not time) box.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Date_Timestamp_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'cmb-timepicker', trailingslashit( CMB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'cmb-scripts' ) );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );

	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_small cmb_datepicker' ); ?> type="text" <?php $this->name_attr(); ?>  value="<?php echo $this->value ? esc_attr( date( 'm\/d\/Y', $this->value ) ) : '' ?>" />

		<?php
	}

	/**
	 * Convert values into UNIX time values and sort.
	 */
	public function parse_save_values() {

		foreach ( $this->values as &$value ) {
			$value = strtotime( $value );
		}

		sort( $this->values );

	}
}
