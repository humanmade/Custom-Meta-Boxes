<?php
/**
 * Standard checkbox field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Checkbox extends CMB_Field {

	/**
	 * Print out field HTML - in this case intentionally empty.
	 */
	public function title() {}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> type="checkbox" <?php $this->name_attr(); ?>  value="1" <?php checked( $this->get_value() ); ?> />
		<label <?php $this->for_attr(); ?>><?php echo esc_html( $this->title ); ?></label>

		<?php
	}
}
