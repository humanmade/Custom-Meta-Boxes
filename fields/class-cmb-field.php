<?php
/**
 * Abstract class for all fields.
 * Subclasses need only override html()
 *
 * @abstract
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

abstract class CMB_Field {

	/**
	 * Current field value.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Current field placement index.
	 *
	 * @var int
	 */
	public $field_index = 0;

	/**
	 * CMB_Field constructor.
	 *
	 * @param string $name Field name/ID.
	 * @param string $title Title to display in field.
	 * @param array  $values Values to populate field(s) with.
	 * @param array  $args Optional. Field definitions/arguments.
	 */
	public function __construct( $name, $title, array $values, $args = array() ) {

		$this->id    = $name;
		$this->name  = $name . '[]';
		$this->title = $title;
		$this->set_arguments( $args );

		// If the field has a custom value populator callback.
		if ( ! empty( $args['values_callback'] ) ) {
			$this->values = call_user_func( $args['values_callback'], get_the_id() );
		} else {
			$this->values = $values;
		}

		$this->value = reset( $this->values );
	}

	/**
	 * Establish baseline default arguments for a field.
	 *
	 * @return array Default arguments.
	 */
	public function default_args() {
		return array(
			'desc'                => '',
			'repeatable'          => false,
			'sortable'            => false,
			'repeatable_max'      => null,
			'show_label'          => false,
			'readonly'            => false,
			'disabled'            => false,
			'default'             => '',
			'cols'                => '12',
			'style'               => '',
			'class'               => '',
			'data_delegate'       => null,
			'save_callback'       => null,
			'capability'          => 'edit_posts',
			'string-repeat-field' => __( 'Add New', 'cmb' ),
			'string-delete-field' => __( 'Remove', 'cmb' ),
			'confirm_delete'      => true,
		);
	}

	/**
	 * Get the default args for the abstract field.
	 * These args are available to all fields.
	 *
	 * @return array $args
	 */
	public function get_default_args() {

		/**
		 * Filter the default arguments passed by a field class.
		 *
		 * @param array $args default field arguments.
		 * @param string $class Field class being called
		 */
		return apply_filters( 'cmb_field_default_args', $this->default_args(), get_class( $this ) );
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		if ( isset( $this->args['sortable'] ) && $this->args['sortable'] && $this->args['repeatable'] ) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

	}

	/**
	 * Enqueue all styles required by the field.
	 *
	 * @uses wp_enqueue_style()
	 */
	public function enqueue_styles() {}

	/**
	 * Output the field input ID attribute.
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @param  string $append Optional. ID to place.
	 */
	public function id_attr( $append = null ) {

		/**
		 * Modify the id attribute of a field.
		 *
		 * @param string $id ID Attribute
		 * @param string $append ID to place.
		 * @param array $args Arguments for this particular field.
		 */
		$id = apply_filters( 'cmb_field_id_attribute', $this->get_the_id_attr( $append ), $append, $this->args );

		printf( 'id="%s"', esc_attr( $id ) );

	}

	/**
	 * Output the for attribute for the field.
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @param  string $append Optional. ID to place.
	 * @return string Modified id attribute contents
	 */
	public function get_the_id_attr( $append = null ) {

		$id = $this->id;

		if ( isset( $this->parent ) ) {
			$parent_id = preg_replace( '/cmb\-field\-(\d+|x)/', 'cmb-group-$1', $this->parent->get_the_id_attr() );
			$id = $parent_id . '[' . $id . ']';
		}

		$id .= '-cmb-field-' . $this->field_index;

		if ( ! is_null( $append ) ) {
			$id .= '-' . $append;
		}

		$id = str_replace( array( '[', ']', '--' ), '-', $id );

		return $id;

	}

	/**
	 * Output the field input ID attribute value.
	 *
	 * If multiple inputs are required for a single field,
	 * use the append parameter to add unique identifier.
	 *
	 * @see get_the_id_attr
	 *
	 * @param string $append Optional. for value to place.
	 */
	public function for_attr( $append = null ) {

		/**
		 * Modify the for attribute of a field.
		 *
		 * @param string $for For attribute
		 * @param array $args Arguments for this particular field.
		 */
		$for = apply_filters( 'cmb_field_for_attribute', $this->get_the_id_attr( $append ), $this->args );

		printf( 'for="%s"', esc_attr( $for ) );

	}

	/**
	 * Output HTML name attribute for a field.
	 *
	 * @see get_the_name_attr
	 *
	 * @param string $append Optional. Name to place.
	 */
	public function name_attr( $append = null ) {

		/**
		 * Modify the name attribute of a field.
		 *
		 * @param string $name Name attribute
		 * @param array $args Arguments for this particular field.
		 */
		$name = apply_filters( 'cmb_field_name_attribute', $this->get_the_name_attr( $append ), $this->args );

		printf( 'name="%s"', esc_attr( $name ) );

	}

	/**
	 * Get the name attribute contents for a field.
	 *
	 * @param null $append Optional. Name to place.
	 * @return string Name attribute contents.
	 */
	public function get_the_name_attr( $append = null ) {

		$name = str_replace( '[]', '', $this->name );

		if ( isset( $this->parent ) ) {
			$parent_name = preg_replace( '/cmb\-field\-(\d+|x)/', 'cmb-group-$1', $this->parent->get_the_name_attr() );
			$name = $parent_name . '[' . $name . ']';
		}

		$name .= "[cmb-field-$this->field_index]";

		if ( ! is_null( $append ) ) {
			$name .= $append;
		}

		return $name;

	}

	/**
	 * Output class attribute for a field.
	 *
	 * @param string $classes Optional. Classes to assign to the field.
	 */
	public function class_attr( $classes = '' ) {

		// Combine any passed-in classes and the ones defined in the arguments and sanitize them.
		$all_classes = array_unique( explode( ' ', $classes . ' ' . $this->args['class'] ) );
		$classes     = array_map( 'sanitize_html_class', array_filter( $all_classes ) );

		/**
		 * Modify the classes assigned to a field.
		 *
		 * @param array $classes Classes currently assigned to the field
		 * @param array $args Arguments for this particular field.
		 */
		$classes = apply_filters( 'cmb_field_classes', $classes, $this->args );

		if ( $classes = implode( ' ', $classes ) ) { ?>

			class="<?php echo esc_attr( $classes ); ?>"

		<?php }

	}

	/**
	 * Get JS Safe ID.
	 *
	 * For use as a unique field identifier in javascript.
	 *
	 * @return string JS-escaped ID string.
	 */
	public function get_js_id() {

		return str_replace( array( '-', '[', ']', '--' ),'_', $this->get_the_id_attr() );

	}

	/**
	 * Print one or more HTML5 attributes for a field.
	 *
	 * @param array $attrs Optional. Attributes to define in the field.
	 */
	public function boolean_attr( $attrs = array() ) {

		if ( $this->args['readonly'] ) {
			$attrs[] = 'readonly';
		}

		if ( $this->args['disabled'] ) {
			$attrs[] = 'disabled';
		}

		$attrs = array_filter( array_unique( $attrs ) );

		/**
		 * Modify any boolean attributes assigned to a field.
		 *
		 * @param array $attrs Boolean attributes.
		 * @param array $args Arguments for this particular field.
		 */
		$attrs = apply_filters( 'cmb_field_boolean_attributes', $attrs, $this->args );

		foreach ( $attrs as $attr ) {
			echo esc_html( $attr ) . '="' . esc_attr( $attr ) . '"';
		}

	}

	/**
	 * Check if this field has a data delegate set
	 *
	 * @return boolean Set or turned off.
	 */
	public function has_data_delegate() {
		return (bool) $this->args['data_delegate'];
	}

	/**
	 * Get the array of data from the data delegate.
	 *
	 * @return array mixed
	 */
	protected function get_delegate_data() {

		if ( $this->args['data_delegate'] ) {
			return call_user_func_array( $this->args['data_delegate'], array( $this ) );
		}

		return array();

	}

	/**
	 * Get the existing or default value for a field.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return ( $this->value || '0' === $this->value  ) ? $this->value : $this->args['default'];
	}

	/**
	 * Get multiple values for a field.
	 *
	 * @return array
	 */
	public function get_values() {
		return $this->values;
	}

	/**
	 * Define multiple values for a field and completely remove the singular value variable.
	 *
	 * @param array $values Field values.
	 */
	public function set_values( array $values ) {

		$this->values = $values;

		unset( $this->value );

	}

	/**
	 * Parse and validate an array of values.
	 *
	 * Meant to be extended.
	 */
	public function parse_save_values() {}

	/**
	 * Parse and validate a single value.
	 *
	 * Meant to be extended.
	 */
	public function parse_save_value() {}

	/**
	 * Save values for the field.
	 *
	 * @todo this surely only works for posts
	 * @todo why do values need to be passed in, they can already be passed in on construct
	 *
	 * @param int   $post_id Post ID.
	 * @param array $values Values to save.
	 */
	public function save( $post_id, $values ) {

		// Don't save readonly values.
		if ( $this->args['readonly'] ) {
			return;
		}

		$this->values = $values;
		$this->parse_save_values();

		// Allow override from args.
		if ( ! empty( $this->args['save_callback'] ) ) {

			call_user_func( $this->args['save_callback'], $this->values, $post_id );

			return;

		}

		// If we are not on a post edit screen.
		if ( ! $post_id ) {
			return;
		}

		delete_post_meta( $post_id, $this->id );

		foreach ( $this->values as $v ) {

			$this->value = $v;
			$this->parse_save_value();

			if ( $this->value || '0' === $this->value ) {
				add_post_meta( $post_id, $this->id, $this->value );
			}
		}
	}

	/**
	 * Check whether the current field should or should not be displayed.
	 *
	 * @param int $post_id Post ID to check against.
	 */
	public function is_displayed( $post_id ) {
		return current_user_can( $this->args['capability'], $post_id );
	}

	/**
	 * Print title for field.
	 */
	public function title() {

		if ( $this->title ) : ?>

			<div class="field-title">
				<label <?php $this->for_attr(); ?>>
					<?php echo esc_html( $this->title ); ?>
				</label>
			</div>

		<?php endif;

	}

	/**
	 * Print description for field.
	 */
	public function description() {

		if ( ! empty( $this->args['desc'] ) ) : ?>

			<div class="cmb_metabox_description">
				<?php echo wp_kses_post( $this->args['desc'] ); ?>
			</div>

		<?php endif;

	}

	/**
	 * Print out a field.
	 */
	public function display() {

		// If there are no values, we need to start with an empty string since we're foreaching through.
		if ( ! $this->get_values() && ! $this->args['repeatable'] ) {
			$values = array( '' );
		} else {
			$values = $this->get_values(); // Make PHP5.4 >= happy.
			$values = empty( $values ) ? array( '' ) : $values;
		}

		// Print title if necessary.
		$this->title();

		// Print description if necessary.
		$this->description();

		$i = 0;

		// If the map field is not repeatable then we need to wrap it within an
		// array to remove difference in data structure between a repeater field and normal
		if ( isset( $this->args['type'] ) && 'gmap' === $this->args['type'] ) {
			if ( $this->args['repeatable'] === false && ! isset( $this->parent ) ) {
				$values = array( $values );
			}

			// If its within a group then we need to unwrap it from the group,
			if ( $this->args['repeatable'] === false && isset( $this->parent ) ) {
				$values = $values[0];

				// Transitions code for data formats see:
				// https://github.com/humanmade/Custom-Meta-Boxes/issues/422
				if ( count( $values ) !== 1 ) {
					$values = array( $values );
				}
			}
		}

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
		if ( $this->args['repeatable'] ) {
			$this->repeatable_button_markup();
		}
	}

	/**
	 * Markup to print an "add" button and for a repeatable field.
	 */
	protected function repeatable_button_markup() {
		// X used to distinguish hidden fields.
		$this->field_index = 'x';
		$this->value       = '';
		?>

		<div class="field-item hidden" data-class="<?php echo esc_attr( get_class( $this ) ); ?>" style="position: relative; <?php echo esc_attr( $this->args['style'] ); ?>">

			<?php $this->delete_button_markup(); ?>

			<?php $this->html(); ?>

		</div>

		<button class="button repeat-field"><?php echo esc_html( $this->args['string-repeat-field'] ); ?></button>

		<?php
	}

	/**
	 * Markup to print a "remove" button for a repeatable field.
	 */
	protected function delete_button_markup() {
		?>
		<button class="cmb-delete-field" title="<?php echo esc_attr( $this->args['string-delete-field'] ); ?>">
			<span class="cmb-delete-field-icon">&times;</span>
			<?php echo esc_html( $this->args['string-delete-field'] ); ?>
		</button>
		<?php
	}

	/**
	 * Determine whether we're creating a new object or on an existing object.
	 *
	 * This is used so that the plugin has some awareness of whether we're dealing with an
	 * existing or new object. This is important for things like knowing whether or not to
	 * load default values when populating a field view.
	 *
	 * @return bool|null True if is new, false if existing, and null if not in the admin.
	 */
	protected function is_new_object() {
		$screen = get_current_screen();
		if ( null === $screen || ! $screen->in_admin() ) {
			return null;
		}

		$id = $GLOBALS['hook_suffix'];
		$id = ( '.php' == substr( $id, -4 ) ) ? substr( $id, 0, -4 ) : $id;
		if ( 'post-new' === $id || 'link-add' === $id || 'media-new' === $id || 'user-new' === $id ) {
			return true;
		}

		return false;
	}

	/**
	 * Setup arguments for the class.
	 *
	 * @param $arguments
	 */
	public function set_arguments( $arguments ) {
		// Initially set arguments up.
		$this->args = wp_parse_args( $arguments, $this->get_default_args() );

		// Support the deprecated argument: 'std'
		if ( ! empty( $this->args['std'] ) && empty( $this->args['default'] ) ) {
			$this->args['default'] = $this->args['std'];
			_deprecated_argument( 'CMB_Field', '0.9', "field argument 'std' is deprecated, use 'default' instead" );
		}

		if ( ! empty( $this->args['options'] ) && is_array( reset( $this->args['options'] ) ) ) {
			$re_format = array();
			foreach ( $this->args['options'] as $option ) {
				$re_format[ $option['value'] ] = $option['name'];
			}
			$this->args['options'] = $re_format;
		}
	}
}
