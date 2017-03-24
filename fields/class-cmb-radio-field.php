<?php
/**
 * Standard radio field.
 *
 * Args:
 *  - bool "inline" - display the radio buttons inline
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Radio_Field extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'options' => array(),
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->has_data_delegate() ) {
			$this->args['options'] = $this->get_delegate_data();
		} ?>

		<?php foreach ( $this->args['options'] as $key => $value ) : ?>

			<input <?php $this->id_attr( 'item-' . $key ); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> type="radio" <?php $this->name_attr(); ?>  value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $this->get_value() ); ?> />
			<label <?php $this->for_attr( 'item-' . $key ); ?> style="margin-right: 20px;">
				<?php echo esc_html( $value ); ?>
			</label>

		<?php endforeach; ?>

		<?php
	}
}
