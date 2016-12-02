<?php
/**
 * Hidden field type.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

/**
 * Hidden field type.
 *
 * @since 1.1.0
 *
 * @extends CMB_Field
 */
class CMB_Hidden_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input type="hidden" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}