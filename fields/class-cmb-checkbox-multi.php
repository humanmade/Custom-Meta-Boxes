<?php
/**
 * Class CMB_Checkbox_Multi
 *
 * @since 1.1.0
 *
 * @extends CMB_Fields
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Checkbox_Multi extends CMB_Field {

	/**
	 * Print out a field.
	 */
	public function display() {

		// Print title if necessary.
		$this->title();

		// Print description if necessary.
		$this->description();

		if ( $this->args['repeatable'] ) {

			$values = ( $this->get_values() ) ? $this->get_values() : array();

			$i = 0;

			foreach ( $values as $key => $value ) {

				$this->field_index = $i;
				$this->value       = $value;
				?>

				<div class="field-item" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="position: relative; <?php echo esc_attr( $this->args['style'] ); ?>">

					<?php if ( $this->args['repeatable'] ) {
						$this->delete_button_markup();
					} ?>
					<?php $this->html(); ?>

				</div>

				<?php

				$i++;
			}

			// Insert a hidden one if it's repeatable.
			$this->repeatable_button_markup();
		} else {

			$this->value = $this->get_values();
			?>

			<div class="field-item" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="position: relative; <?php echo esc_attr( $this->args['style'] ); ?>">

				<?php $this->html(); ?>

			</div>

			<?php
		}
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->has_data_delegate() ) {
			$this->args['options'] = $this->get_delegate_data();
		}

		// Whoops, someone forgot to add some options. We can't do anything without options.
		if ( empty( $this->args['options'] ) ) {
			return;
		}

		foreach ( $this->args['options'] as $i => $label ) :

			$value = $this->get_values();
			?>

			<div class="cmb-checkbox-wrap">

				<input type="checkbox"
					<?php $this->id_attr( 'item-' . $i ); ?>
					<?php $this->boolean_attr(); ?>
					<?php $this->class_attr(); ?>
					<?php $this->name_attr( '[' . $i . ']' ); ?>
					<?php checked( isset( $value[ $i ] ) ); ?>
				/>

				<label <?php $this->for_attr( 'item-' . $i ); ?>>
					<?php echo esc_html( $label ); ?>
				</label>

			</div>

		<?php
		endforeach;
	}

	/**
	 * Get multiple values for the checkbox-multi field.
	 *
	 * @return array
	 */
	public function get_values() {

		// We always want to fetch the existing (possibly empty) values if it's an existing object.
		if ( ! $this->is_new_object() ) {
			return $this->values;
		}

		return array_flip( (array) $this->args['default'] );
	}
}
