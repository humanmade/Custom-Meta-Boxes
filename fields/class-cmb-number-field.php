<?php
/**
 * Number meta box.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Number_Field extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'step' => '',
				'min' => '',
				'max' => '',
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input step="<?php echo esc_attr( $this->args['step'] ); ?>" <?php echo '' !== $this->args['min'] ? printf( 'min="%d"', $this->args['min'] ) : ''; ?> <?php echo '' !== $this->args['max'] ? printf( 'max="%d"', $this->args['max'] ) : ''; ?> type="number" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_number code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}
