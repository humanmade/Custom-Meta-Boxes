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
				'min'  => '',
				'max'  => '',
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		$attrs = [];
		$attrs[] = '' !== $this->args['step'] ? sprintf( 'step="%g"', $this->args['step'] ) : '';
		$attrs[] = '' !== $this->args['min'] ? sprintf( 'min="%g"', $this->args['min'] ) : '';
		$attrs[] = '' !== $this->args['max'] ? sprintf( 'max="%g"', $this->args['max'] ) : '';
		?>

		<input <?php echo implode( ' ', $attrs ); ?> type="number" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_number code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}
