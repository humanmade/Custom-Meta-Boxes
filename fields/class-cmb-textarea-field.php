<?php
/**
 * Standard text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Textarea_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<textarea <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> rows="<?php echo ! empty( $this->args['rows'] ) ? esc_attr( $this->args['rows'] ) : 4; ?>" <?php $this->name_attr(); ?>><?php echo esc_textarea( $this->get_value() ); ?></textarea>

		<?php
	}
}
