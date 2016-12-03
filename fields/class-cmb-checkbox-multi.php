<?php
/**
 * Checkbox-Multi field type.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

/**
 * Class CMB_Checkbox_Multi
 *
 * @since 1.1.0
 *
 * @extends CMB_Fields
 */
class CMB_Checkbox_Multi extends CMB_Field {

	public function html() {

		if ( $this->has_data_delegate() ) {
			$this->args['options'] = $this->get_delegate_data();
		}

		foreach ( $this->args['options'] as $i => $label ) :

			$value = $this->get_value();

			?>

			<div class="cmb-checkbox-wrap">

				<input
					type="checkbox"
					<?php $this->id_attr( 'item-' . $i ); ?>
					<?php $this->boolean_attr(); ?>
					<?php $this->class_attr(); ?>
					<?php $this->name_attr( '[item-' . $i . ']' ); ?>
					<?php checked( isset( $value[ 'item-' . $i ] ) ); ?>
				/>

				<label <?php $this->for_attr( 'item-' . $i ); ?>>
					<?php echo esc_html( $label ); ?>
				</label>

			</div>

		<?php endforeach;

	}

}