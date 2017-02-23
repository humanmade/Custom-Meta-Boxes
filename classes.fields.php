<?php
/**
 * All HM CMB field classes and definitions.
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

/**
 * Abstract class for all fields.
 * Subclasses need only override html()
 *
 * @abstract
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
		$this->args  = wp_parse_args( $args, $this->get_default_args() );

		// Deprecated argument: 'std'
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
	public function &get_values() {
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
	 */
	public function is_displayed() {
		return current_user_can( $this->args['capability'] );
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
		if ( isset( $this->args['type'] ) && 'gmap' == $this->args['type'] ) {
			$values = array( $values );
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
		if ( null === $screen || ! $screen->in_admin ) {
			return null;
		}

		$id = $GLOBALS['hook_suffix'];
		$id = ( '.php' == substr( $id, -4 ) ) ? substr( $id, 0, -4 ) : $id;
		if ( 'post-new' === $id || 'link-add' === $id || 'media-new' === $id || 'user-new' === $id ) {
			return true;
		}

		return false;
	}
}

/**
 * Standard text field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Text_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input type="text" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}

/**
 * Small text field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Text_Field
 */
class CMB_Text_Small_Field extends CMB_Text_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$this->args['class'] .= ' cmb_text_small';

		parent::html();

	}
}

/**
 * Field for image upload / file upload.
 *
 * @todo ability to set image size (preview image) from caller
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_File_Field extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'library-type' => array(
					'video',
					'audio',
					'text',
					'application',
				),
			)
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	function enqueue_scripts() {

		global $post_ID;
		$post_ID = isset( $post_ID ) ? (int) $post_ID : 0;

		parent::enqueue_scripts();

		wp_enqueue_media( array( 'post' => $post_ID ) );
		wp_enqueue_script( 'cmb-file-upload', trailingslashit( CMB_URL ) . 'js/file-upload.js', array( 'jquery', 'cmb-scripts' ), CMB_VERSION );

	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->get_value() ) {
			$src = wp_mime_type_icon( $this->get_value() );
			if ( strpos( $src, site_url() !== false ) ) {
				$size = getimagesize( str_replace( site_url(), ABSPATH, $src ) );
			} else {
				$size = null;
			}
			$icon_img = '<img src="' . $src . '" ' . ( $size ? $size[3] : '' ) . ' />';
		}

		$data_type = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="cmb-file-wrap" <?php echo 'data-type="' . esc_attr( $data_type ) . '"'; ?>>

			<div class="cmb-file-wrap-placeholder"></div>

			<button class="button cmb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>">
				<?php esc_html_e( 'Add File', 'cmb' ); ?>
			</button>

			<div class="cmb-file-holder type-file <?php echo $this->get_value() ? '' : 'hidden'; ?>">

				<?php if ( $this->get_value() ) : ?>

					<?php
					if ( isset( $icon_img ) ) {
						echo $icon_img;
					}
					?>

					<div class="cmb-file-name">
						<strong><?php echo esc_html( basename( get_attached_file( $this->get_value() ) ) ); ?></strong>
					</div>

				<?php endif; ?>

			</div>

			<button class="cmb-remove-file button <?php echo $this->get_value() ? '' : 'hidden'; ?>">
				<?php esc_html_e( 'Remove', 'cmb' ); ?>
			</button>

			<input type="hidden"
				<?php $this->class_attr( 'cmb-file-upload-input' ); ?>
				<?php $this->name_attr(); ?>
				value="<?php echo esc_attr( $this->value ); ?>"
			/>

		</div>

		<?php
	}
}

/**
 * Field for image upload.
 *
 * @since 1.0.0
 *
 * @extends CMB_File_Field
 */
class CMB_Image_Field extends CMB_File_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'size' => 'thumbnail',
				'library-type' => array(
					'image',
				),
				'show_size' => false,
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		if ( $this->get_value() ) {
			$image = wp_get_attachment_image_src( $this->get_value(), $this->args['size'], true );
		}

		// Convert size arg to array of width, height, crop.
		$size = $this->parse_image_size( $this->args['size'] );

		// Inline styles.
		$styles              = sprintf( 'width: %1$dpx; height: %2$dpx; line-height: %2$dpx', intval( $size['width'] ), intval( $size['height'] ) );
		$placeholder_styles  = sprintf( 'width: %dpx; height: %dpx;', intval( $size['width'] ) - 8, intval( $size['height'] ) - 8 );

		$data_type           = ( ! empty( $this->args['library-type'] ) ? implode( ',', $this->args['library-type'] ) : null );

		?>

		<div class="cmb-file-wrap" style="<?php echo esc_attr( $styles ); ?>" data-type="<?php echo esc_attr( $data_type ); ?>" data-max-width="<?php echo absint( $size['width'] ); ?>">

			<div class="cmb-file-wrap-placeholder" style="<?php echo esc_attr( $placeholder_styles ); ?>">

				<?php if ( $this->args['show_size'] ) : ?>
					<span class="dimensions">
						<?php printf( '%dpx &times; %dpx', intval( $size['width'] ), intval( $size['height'] ) ); ?>
					</span>
				<?php endif; ?>

			</div>

			<button class="button cmb-file-upload <?php echo esc_attr( $this->get_value() ) ? 'hidden' : '' ?>" data-nonce="<?php echo wp_create_nonce( 'cmb-file-upload-nonce' ); ?>">
				<?php esc_html_e( 'Add Image', 'cmb' ); ?>
			</button>

			<div class="cmb-file-holder type-img <?php echo $this->get_value() ? '' : 'hidden'; ?>" data-crop="<?php echo (bool) $size['crop']; ?>">

				<?php if ( ! empty( $image ) ) : ?>
					<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo intval( $image[1] ); ?>" height="<?php echo intval( $image[2] ); ?>" />
				<?php endif; ?>

			</div>

			<button class="cmb-remove-file button <?php echo $this->get_value() ? '' : 'hidden'; ?>">
				<?php esc_html_e( 'Remove', 'cmb' ); ?>
			</button>

			<input type="hidden"
				<?php $this->class_attr( 'cmb-file-upload-input' ); ?>
				<?php $this->name_attr(); ?>
				value="<?php echo esc_attr( $this->value ); ?>"
			/>

		</div>

	<?php }

	/**
	 * Parse the size argument to get pixel width, pixel height and crop information.
	 *
	 * @param string $size Size of image requested.
	 * @return array width, height, crop
	 */
	private function parse_image_size( $size ) {

		// Handle string for built-in image sizes.
		if ( is_string( $size ) && in_array( $size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			return array(
				'width'  => get_option( $size . '_size_w' ),
				'height' => get_option( $size . '_size_h' ),
				'crop'   => get_option( $size . '_crop' ),
			);
		}

		// Handle string for additional image sizes.
		global $_wp_additional_image_sizes;
		if ( is_string( $size ) && isset( $_wp_additional_image_sizes[ $size ] ) ) {
			return array(
				'width'  => $_wp_additional_image_sizes[ $size ]['width'],
				'height' => $_wp_additional_image_sizes[ $size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $size ]['crop'],
			);
		}

		// Handle default WP size format.
		if ( is_array( $size ) && isset( $size[0] ) && isset( $size[1] ) ) {
			$size = array( 'width' => $size[0], 'height' => $size[1] );
		}

		return wp_parse_args( $size, array(
			'width'  => get_option( 'thumbnail_size_w' ),
			'height' => get_option( 'thumbnail_size_h' ),
			'crop'   => get_option( 'thumbnail_crop' ),
		) );

	}

	/**
	 * Ajax callback for outputing an image src based on post data.
	 *
	 * @return null
	 */
	static function request_image_ajax_callback() {

		if ( ! ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'cmb-file-upload-nonce' ) ) ) {
			return;
		}

		$id = absint( $_POST['id'] );

		$size = array(
			intval( $_POST['width'] ),
			intval( $_POST['height'] ),
			'crop' => (bool) $_POST['crop'],
		);

		$image = wp_get_attachment_image_src( $id, $size );
		echo esc_url( reset( $image ) );

		// This is required to return a proper result.
		die();
	}
}
add_action( 'wp_ajax_cmb_request_image', array( 'CMB_Image_Field', 'request_image_ajax_callback' ) );

/**
 * Number meta box.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
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
			)
		);
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input step="<?php echo esc_attr( $this->args['step'] ); ?>" type="number" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_number code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}

/**
 * Standard text meta box for a URL.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_URL_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input type="text" <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_url code' ); ?> <?php $this->name_attr(); ?> value="<?php echo esc_attr( esc_url( $this->value ) ); ?>" />

		<?php
	}
}

/**
 * Date picker meta box field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Date_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		// If the user has set a cols arg of less than 6 columns, allow the intput
		// to go full-width.
		$classes = ( is_int( $this->args['cols'] ) && $this->args['cols'] <= 6 ) ? 'cmb_datepicker' : 'cmb_text_small cmb_datepicker' ;
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( $classes ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value ); ?>" />

		<?php
	}
}

/**
 * Time picker meta box field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Time_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'cmb-timepicker', trailingslashit( CMB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'cmb-scripts' ) );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_small cmb_timepicker' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->value ); ?>"/>

		<?php
	}
}

/**
 * Date picker for date only (not time) box.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Date_Timestamp_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'cmb-timepicker', trailingslashit( CMB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'cmb-scripts' ) );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );

	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_small cmb_datepicker' ); ?> type="text" <?php $this->name_attr(); ?>  value="<?php echo $this->value ? esc_attr( date( 'm\/d\/Y', $this->value ) ) : '' ?>" />

		<?php
	}

	/**
	 * Convert values into UNIX time values and sort.
	 */
	public function parse_save_values() {

		foreach ( $this->values as &$value ) {
			$value = strtotime( $value );
		}

		sort( $this->values );

	}
}

/**
 * Date picker for date and time (seperate fields) box.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Datetime_Timestamp_Field extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_style( 'cmb-jquery-ui', trailingslashit( CMB_URL ) . 'css/vendor/jquery-ui/jquery-ui.css', '1.10.3' );

		wp_enqueue_script( 'cmb-timepicker', trailingslashit( CMB_URL ) . 'js/jquery.timePicker.min.js', array( 'jquery', 'cmb-scripts' ) );
		wp_enqueue_script( 'cmb-datetime', trailingslashit( CMB_URL ) . 'js/field.datetime.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr( 'date' ); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_small cmb_datepicker' ); ?> type="text" <?php $this->name_attr( '[date]' ); ?>  value="<?php echo $this->value ? esc_attr( date( 'm\/d\/Y', $this->value ) ) : '' ?>" />
		<input <?php $this->id_attr( 'time' ); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_text_small cmb_timepicker' ); ?> type="text" <?php $this->name_attr( '[time]' ); ?> value="<?php echo $this->value ? esc_attr( date( 'h:i A', $this->value ) ) : '' ?>" />

		<?php
	}

	/**
	 * Convert values into UNIX time values and sort.
	 */
	public function parse_save_values() {

		// Convert all [date] and [time] values to a unix timestamp.
		// If date is empty, assume delete. If time is empty, assume 00:00.
		foreach ( $this->values as $key => &$value ) {
			if ( empty( $value['date'] ) ) {
				unset( $this->values[ $key ] );
			} else {
				$value = strtotime( $value['date'] . ' ' . $value['time'] );
			}
		}

		$this->values = array_filter( $this->values );
		sort( $this->values );

		parent::parse_save_values();

	}
}

/**
 * Standard text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Textarea_Field extends CMB_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<textarea <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> rows="<?php echo ! empty( $this->args['rows'] ) ? esc_attr( $this->args['rows'] ) : 4; ?>" <?php $this->name_attr(); ?>><?php echo esc_textarea( $this->value ); ?></textarea>

		<?php
	}
}

/**
 * Code style text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 *
 * @since 1.0.0
 *
 * @extends CMB_Textarea_Field
 */
class CMB_Textarea_Field_Code extends CMB_Textarea_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$this->args['class'] .= ' code';

		parent::html();

	}
}

/**
 *  Colour picker
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Color_Picker extends CMB_Field {

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-colorpicker', trailingslashit( CMB_URL ) . 'js/field.colorpicker.js', array( 'jquery', 'wp-color-picker', 'cmb-scripts' ), CMB_VERSION );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {
		?>

		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr( 'cmb_colorpicker cmb_text_small' ); ?> type="text" <?php $this->name_attr(); ?> value="<?php echo esc_attr( $this->get_value() ); ?>" />

		<?php
	}
}

/**
 * Standard radio field.
 *
 * Args:
 *  - bool "inline" - display the radio buttons inline
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
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

/**
 * Standard checkbox field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
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

		<input type="hidden" <?php $this->name_attr(); ?> value="0" />
		<input <?php $this->id_attr(); ?> <?php $this->boolean_attr(); ?> <?php $this->class_attr(); ?> type="checkbox" <?php $this->name_attr(); ?>  value="1" <?php checked( $this->get_value() ); ?> />
		<label <?php $this->for_attr(); ?>><?php echo esc_html( $this->title ); ?></label>

		<?php
	}
}

/**
 * Standard title used as a splitter.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_Title extends CMB_Field {

	/**
	 * Print out field HTML - in this case we only want a title.
	 */
	public function title() {
		?>

		<div class="field-title">
			<h2 <?php $this->class_attr(); ?>>
				<?php echo esc_html( $this->title ); ?>
			</h2>
		</div>

		<?php

	}

	/**
	 * Placeholder for abstracted method.
	 */
	public function html() {}
}

/**
 * WYSIWYG field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 */
class CMB_wysiwyg extends CMB_Field {

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
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-wysiwyg', trailingslashit( CMB_URL ) . 'js/field-wysiwyg.js', array( 'jquery', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$id   = $this->get_the_id_attr();
		$name = $this->get_the_name_attr();

		$field_id = $this->get_js_id();

		printf(
			'<div class="cmb-wysiwyg" data-id="%s" data-name="%s" data-field-id="%s">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $field_id )
		);

		if ( $this->is_placeholder() ) {

			// For placeholder, output the markup for the editor in a JS var.
			ob_start();
			$this->args['options']['textarea_name'] = 'cmb-placeholder-name-' . $field_id;
			wp_editor( '', 'cmb-placeholder-id-' . $field_id, $this->args['options'] );
			$editor = ob_get_clean();
			$editor = str_replace( array( "\n", "\r" ), '', $editor );
			$editor = str_replace( array( "'" ), '"', $editor );

			?>

			<script>
				if ( 'undefined' === typeof( cmb_wysiwyg_editors ) )
					var cmb_wysiwyg_editors = {};
				cmb_wysiwyg_editors.<?php echo esc_js( $field_id ); ?> = <?php echo wp_json_encode( $editor ); ?>;
			</script>

			<?php

		} else {

			$this->args['options']['textarea_name'] = $name;
			echo wp_editor( $this->get_value(), $id, $this->args['options'] );

		}

		echo '</div>';

	}

	/**
	 * Check if this is a placeholder field.
	 * Either the field itself, or because it is part of a repeatable group.
	 *
	 * @return bool
	 */
	public function is_placeholder() {

		if ( isset( $this->parent ) && ! is_int( $this->parent->field_index ) ) {
			return true;
		} else {
			return ! is_int( $this->field_index );
		}

	}
}

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

/**
 * Taxonomy-specific select field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Select
 */
class CMB_Taxonomy extends CMB_Select {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'taxonomy'   => '',
				'hide_empty' => false,
			)
		);
	}

	/**
	 * CMB_Taxonomy constructor.
	 */
	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

	}

	/**
	 * Retrieve custom field data.
	 *
	 * @return array Terms for field data.
	 */
	public function get_delegate_data() {

		$terms = $this->get_terms();

		if ( is_wp_error( $terms ) ) {
			return array();
		}

		$term_options = array();

		foreach ( $terms as $term ) {
			$term_options[ $term->term_id ] = $term->name;
		}

		return $term_options;

	}

	/**
	 * Get terms for select field.
	 *
	 * @todo::cache this or find a cached method
	 *
	 * @return array|int|WP_Error
	 */
	private function get_terms() {

		return get_terms( $this->args['taxonomy'], array( 'hide_empty' => $this->args['hide_empty'] ) );

	}
}

/**
 * Post Select field.
 *
 * @supports "data_delegate"
 * @args
 *     'options'     => array Array of options to show in the select, optionally use data_delegate instead
 *     'allow_none'   => bool Allow no option to be selected (will place a "None" at the top of the select)
 *     'multiple'     => bool whether multiple can be selected
 *
 * @since 1.0.0
 *
 * @extends CMB_Select
 */
class CMB_Post_Select extends CMB_Select {

	/**
	 * CMB_Post_Select constructor.
	 */
	public function __construct() {

		$args = func_get_args();

		call_user_func_array( array( 'parent', '__construct' ), $args );

		if ( ! $this->args['use_ajax'] ) {

			$this->args['data_delegate'] = array( $this, 'get_delegate_data' );

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
				'query'    => array(),
				'use_ajax' => false,
				'multiple' => false,
			)
		);
	}

	/**
	 * Get posts and verify for use in select field.
	 *
	 * @todo:: validate this data before returning.
	 *
	 * @return array Array of posts for field.
	 */
	public function get_delegate_data() {

		$data = array();

		foreach ( $this->get_posts() as $post_id ) {
			$data[ $post_id ] = get_the_title( $post_id );
		}

		return $data;

	}

	/**
	 * Get posts for use in select field.
	 *
	 * @return array
	 */
	private function get_posts() {

		$this->args['query']['fields'] = 'ids';
		$query = new WP_Query( $this->args['query'] );

		return isset( $query->posts ) ? $query->posts : array();

	}

	/**
	 * Format the field values for Select2 to read.
	 */
	public function parse_save_value() {

		// AJAX multi select2 data is submitted as a string of comma separated post IDs.
		// If empty, set to false instead of empty array to ensure the meta entry is deleted.
		if ( $this->args['use_ajax'] && $this->args['multiple'] ) {
			$this->value = ( ! empty( $this->value ) ) ? explode( ',', $this->value ) : false;
		}

	}

	/**
	 * Assemble and output of field HTML.
	 */
	public function output_field() {

		// If AJAX, must use input type not standard select.
		if ( $this->args['use_ajax'] ) :

			?>

			<input
				<?php $this->id_attr(); ?>
				<?php printf( 'value="%s" ', esc_attr( implode( ',' , (array) $this->value ) ) ); ?>
				<?php printf( 'name="%s"', esc_attr( $this->get_the_name_attr() ) ); ?>
				<?php printf( 'data-field-id="%s" ', esc_attr( $this->get_js_id() ) ); ?>
				<?php $this->boolean_attr(); ?>
				class="cmb_select"
				style="width: 100%"
			/>

			<?php

		else :

			parent::output_field();

		endif;

	}

	/**
	 * Output inline scripts to support field.
	 */
	public function output_script() {

		parent::output_script();

		?>

		<script type="text/javascript">

			(function($) {

				if ( 'undefined' === typeof( window.cmb_select_fields ) ) {
					return false;
				}

				// Get options for this field so we can modify it.
				var id = <?php echo json_encode( $this->get_js_id() ); ?>;
				var options = window.cmb_select_fields[id];

				<?php if ( $this->args['use_ajax'] && $this->args['multiple'] ) : ?>
					// The multiple setting is required when using ajax (because an input field is used instead of select)
					options.multiple = true;
				<?php endif; ?>

				<?php if ( $this->args['use_ajax'] && ! empty( $this->value ) ) : ?>

					options.initSelection = function( element, callback ) {

						var data = [];

						<?php if ( $this->args['multiple'] ) : ?>

							<?php foreach ( (array) $this->value as $post_id ) : ?>
								data.push( <?php echo json_encode( array( 'id' => $post_id, 'text' => html_entity_decode( get_the_title( $post_id ) ) ) ); ?> );
							<?php endforeach; ?>

						<?php else : ?>

							data = <?php echo json_encode( array( 'id' => $this->value, 'text' => html_entity_decode( get_the_title( $this->get_value() ) ) ) ); ?>;

						<?php endif; ?>

						callback( data );

					};

				<?php endif; ?>

				<?php if ( $this->args['use_ajax'] ) : ?>

					<?php $this->args['query']['fields'] = 'ids'; ?>

					var ajaxData = {
						action  : 'cmb_post_select',
						post_id : '<?php echo intval( get_the_id() ); ?>', // Used for user capabilty check.
						nonce   : <?php echo json_encode( wp_create_nonce( 'cmb_select_field' ) ); ?>,
						query   : <?php echo json_encode( $this->args['query'] ); ?>
					};

					options.ajax = {
						url: <?php echo json_encode( esc_url( admin_url( 'admin-ajax.php' ) ) ); ?>,
						type: 'POST',
						dataType: 'json',
						data: function( term, page ) {
							ajaxData.query.s = term;
							ajaxData.query.paged = page;
							return ajaxData;
						},
						results : function( results, page ) {
							var postsPerPage = ajaxData.query.posts_per_page = ( 'posts_per_page' in ajaxData.query ) ? ajaxData.query.posts_per_page : ( 'showposts' in ajaxData.query ) ? ajaxData.query.showposts : 10;
							var isMore = ( page * postsPerPage ) < results.total;
							return { results: results.posts, more: isMore };
						}
					}

				<?php endif; ?>

			})( jQuery );

		</script>

		<?php
	}
}

/**
 * AJAX callback for select fields.
 *
 * @todo:: this should be in inside the class.
 */
function cmb_ajax_post_select() {

	$post_id = ! empty( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : false;
	$nonce   = ! empty( $_POST['nonce'] ) ? $_POST['nonce'] : false;
	$args    = ! empty( $_POST['query'] ) ? $_POST['query'] : array();

	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'cmb_select_field' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		echo json_encode( array( 'total' => 0, 'posts' => array() ) );
		exit;
	}

	$args['fields'] = 'ids'; // Only need to retrieve post IDs.

	$query = new WP_Query( $args );

	$json = array( 'total' => $query->found_posts, 'posts' => array() );

	foreach ( $query->posts as $post_id ) {
		array_push( $json['posts'], array( 'id' => $post_id, 'text' => html_entity_decode( get_the_title( $post_id ) ) ) );
	}

	echo json_encode( $json );

	exit;

}
add_action( 'wp_ajax_cmb_post_select', 'cmb_ajax_post_select' );

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

		$fields = &$this->get_fields();
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

		$fields = &$this->get_fields();
		$values = &$this->get_values();

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
	public function &get_fields() {
		return $this->fields;
	}

	/**
	 * Set values for each field in the group.
	 *
	 * @param array $values Existing or default values for all fields.
	 */
	public function set_values( array $values ) {

		$fields       = &$this->get_fields();
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
