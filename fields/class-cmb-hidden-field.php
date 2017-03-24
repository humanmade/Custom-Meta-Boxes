<?php
/**
 * Hidden field type.
 *
 * @since 1.1.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Hidden_Field extends CMB_Field {

	/**
	 * Print out a field wrapper.
	 *
	 * Overriding this as there's no reason nor opportunity to have a repeatable hidden field and we don't
	 * want to display the title and description here so getting rid of all of that logic.
	 */
	public function display() {
		?>

		<div class="field-item" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="display: none; <?php echo esc_attr( $this->args['style'] ); ?>">

			<?php $this->html(); ?>

		</div>

		<?php
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input type="hidden" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}
