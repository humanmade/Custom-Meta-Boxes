<?php
/**
 * Standard text meta box for an email.
 *
 * @since 1.1.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Email_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input type="email" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_email code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}
