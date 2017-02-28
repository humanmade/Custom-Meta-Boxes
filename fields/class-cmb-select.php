<?php
/**
 * Standard select field.
 *
 * @supports "data_delegate"
 * @args
 *     'options'     => array Array of options to show in the select, optionally use data_delegate instead
 *     'allow_none'   => bool|string Allow no option to be selected (will place a "None" at the top of the select)
 *     'multiple'     => bool whether multiple can be selected
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Select extends CMB_Field {

	/**
	 * CMB_Select constructor.
	 */
	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

	}

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'options'         => array(),
				'multiple'        => false,
				'select2_options' => array(),
				'allow_none'      => false,
			)
		);
	}

	/**
	 * Ensure values are saved as an array if multiple is set.
	 */
	public function parse_save_values() {

		if ( isset( $this->parent ) && isset( $this->args['multiple'] ) && $this->args['multiple'] ) {
			$this->values = array( $this->values );
		}

	}

	/**
	 * Get options for field.
	 *
	 * @return mixed
	 */
	public function get_options() {

		if ( $this->has_data_delegate() ) {
			$this->args['options'] = $this->get_delegate_data();
		}

		return $this->args['options'];
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'select2', trailingslashit( CMB_URL ) . 'js/vendor/select2/select2.js', array( 'jquery' ) );
		wp_enqueue_script( 'field-select', trailingslashit( CMB_URL ) . 'js/field.select.js', array( 'jquery', 'select2', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Enqueue all styles required by the field.
	 *
	 * @uses wp_enqueue_style()
	 */
	public function enqueue_styles() {

		parent::enqueue_styles();

		wp_enqueue_style( 'select2', trailingslashit( CMB_URL ) . 'js/vendor/select2/select2.css' );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->has_data_delegate() ) {
			$this->args['options'] = $this->get_delegate_data();
		}

		$this->output_field();

		$this->output_script();

	}

	/**
	 * Compile field HTML.
	 */
	public function output_field() {

		$val = (array) $this->get_value();

		$name = $this->get_the_name_attr();
		$name .= ! empty( $this->args['multiple'] ) ? '[]' : null;

		$none = is_string( $this->args['allow_none'] ) ? $this->args['allow_none'] : __( 'None', 'cmb' );

		?>

		<select
			<?php $this->id_attr(); ?>
			<?php $this->boolean_attr(); ?>
			<?php printf( 'name="%s"', esc_attr( $name ) ); ?>
			<?php printf( 'data-field-id="%s" ', esc_attr( $this->get_js_id() ) ); ?>
			<?php echo ! empty( $this->args['multiple'] ) ? 'multiple' : '' ?>
			<?php $this->class_attr( 'cmb_select' ); ?>
			style="width: 100%"
		>

			<?php if ( $this->args['allow_none'] ) : ?>
				<option value=""><?php echo esc_html( $none ); ?></option>
			<?php endif; ?>

			<?php foreach ( $this->args['options'] as $value => $name ) : ?>
				<option <?php selected( in_array( $value, $val ) ) ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>

		</select>

		<?php
	}

	/**
	 * Output inline scripts to support field.
	 */
	public function output_script() {

		$options = wp_parse_args( $this->args['select2_options'], array(
			'placeholder' => __( 'Type to search', 'cmb' ),
			'allowClear'  => true,
		) );

		?>

		<script type="text/javascript">

			(function($) {

				var options = <?php echo  json_encode( $options ); ?>

				if ( 'undefined' === typeof( window.cmb_select_fields ) )
					window.cmb_select_fields = {};

				var id = <?php echo json_encode( $this->get_js_id() ); ?>;
				window.cmb_select_fields[id] = options;

			})( jQuery );

		</script>

		<?php
	}
}
