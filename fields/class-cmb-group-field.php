<?php
/**
 * Field to group child fields
 * pass $args[fields] array for child fields
 * pass $args['repeatable'] for cloing all child fields (set)
 *
 * @todo remove global $post reference, somehow
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Group_Field extends CMB_Field {

	static $added_js;

	/**
	 * Fields arguments and information.
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * CMB_Group_Field constructor.
	 */
	function __construct() {

		// You can't just put func_get_args() into a function as a parameter.
		$args = func_get_args();
		call_user_func_array( array( 'parent', '__construct' ), $args );

		if ( ! empty( $this->args['fields'] ) ) {
			foreach ( $this->args['fields'] as $f ) {

				$class = _cmb_field_class_for_type( $f['type'] );

				if ( ! empty( $class ) && class_exists( $class ) ) {
					$this->add_field( new $class( $f['id'], $f['name'], array(), $f ) );
				}
			}
		}

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
				'fields'              => array(),
				'string-repeat-field' => __( 'Add New Group', 'cmb' ),
				'string-delete-field' => __( 'Remove Group', 'cmb' ),
			)
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		foreach ( $this->args['fields'] as $f ) {
			$class = _cmb_field_class_for_type( $f['type'] );
			if ( ! empty( $class ) && class_exists( $class ) ) {
				$field = new $class( '', '', array(), $f );
				$field->enqueue_scripts();
			}
		}

	}

	/**
	 * Enqueue all styles required by the field.
	 *
	 * @uses wp_enqueue_style()
	 */
	public function enqueue_styles() {

		parent::enqueue_styles();

		foreach ( $this->args['fields'] as $f ) {
			$class = _cmb_field_class_for_type( $f['type'] );
			if ( ! empty( $class ) && class_exists( $class ) ) {
				$field = new $class( '', '', array(), $f );
				$field->enqueue_styles();
			}
		}

	}

	/**
	 * Display output for group.
	 */
	public function display() {

		global $post;

		$field = $this->args;
		$values = $this->get_values();

		$this->title();
		$this->description();

		if ( ! $this->args['repeatable'] && empty( $values ) ) {
			$values = array( null );
		} else {
			$values = $this->get_values(); // Make PHP5.4 >= happy.
			$values = ( empty( $values ) ) ? array( '' ) : $values;
		}

		$i = 0;
		foreach ( $values as $value ) {

			$this->field_index = $i;
			$this->value = $value;

			?>

			<div class="field-item" data-class="<?php echo esc_attr( get_class( $this ) ) ?>" style="<?php echo esc_attr( $this->args['style'] ); ?>">
				<?php $this->html(); ?>
			</div>

			<?php

			$i++;

		}

		if ( $this->args['repeatable'] ) {

			$this->field_index = 'x'; // X used to distinguish hidden fields.
			$this->value = '';

			?>

			<div class="field-item hidden" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="<?php echo esc_attr( $this->args['style'] ); ?>">
				<?php $this->html(); ?>
			</div>

			<button class="button repeat-field">
				<?php echo esc_html( $this->args['string-repeat-field'] ); ?>
			</button>

		<?php }

	}

	/**
	 * Print out group field HTML.
	 */
	public function html() {

		$fields = $this->get_fields();
		$value  = $this->get_value();

		// Reset all field values.
		foreach ( $fields as $field ) {
			$field->set_values( array() );
		}

		// Set values for this field.
		if ( ! empty( $value ) ) {
			foreach ( $value as $field_id => $field_value ) {
				$field_value = ( ! empty( $field_value ) ) ? $field_value : array();
				if ( ! empty( $fields[ $field_id ] ) ) {
					$fields[ $field_id ]->set_values( (array) $field_value );
				}
			}
		}

		?>

		<?php if ( $this->args['repeatable'] ) : ?>
			<button class="cmb-delete-field">
				<span class="cmb-delete-field-icon">&times;</span>
				<?php echo esc_html( $this->args['string-delete-field'] ); ?>
			</button>
		<?php endif; ?>

		<?php CMB_Meta_Box::layout_fields( $fields ); ?>

	<?php }

	/**
	 * Parse values individually based on what kind of field they are.
	 */
	public function parse_save_values() {

		$fields = $this->get_fields();
		$values = $this->get_values();

		foreach ( $values as &$group_value ) {
			foreach ( $group_value as $field_id => &$field_value ) {

				if ( ! isset( $fields[ $field_id ] ) ) {
					$field_value = array();
					continue;
				}

				$field = $fields[ $field_id ];
				$field->set_values( $field_value );
				$field->parse_save_values();

				$field_value = $field->get_values();

				// if the field is a repeatable field, store the whole array of them, if it's not repeatble,
				// just store the first (and only) one directly.
				if ( ! $field->args['repeatable'] ) {
					$field_value = reset( $field_value );
				}
			}
		}

		$this->set_values( $values );
	}

	/**
	 * Add assigned fields to group data.
	 *
	 * @param CMB_Field $field Field object.
	 */
	public function add_field( CMB_Field $field ) {
		$field->parent = $this;
		$this->fields[ $field->id ] = $field;
	}

	/**
	 * Assemble all defined fields for group.
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Set values for each field in the group.
	 *
	 * @param array $values Existing or default values for all fields.
	 */
	public function set_values( array $values ) {

		$fields       = $this->get_fields();
		$this->values = $values;

		// Reset all field values.
		foreach ( $fields as $field ) {
			$field->set_values( array() );
		}

		foreach ( $values as $value ) {
			foreach ( $value as $field_id => $field_value ) {
				$fields[ $field_id ]->set_values( (array) $field_value );
			}
		}

	}
}
