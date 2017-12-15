<?php
/**
 * Base functionality for HM CMB plugin.
 *
 * @since 1.0.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Meta_Box {

	/**
	 * Meta box set collection data.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	protected $_meta_box;

	/**
	 * Fields in a collection.
	 *
	 * @access protected
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * CMB_Meta_Box constructor.
	 *
	 * @param array $meta_box Meta box collection.
	 */
	function __construct( $meta_box ) {

		$this->_meta_box = $meta_box;

		// If collection ID is missing, assign the title sanitized to the ID.
		if ( empty( $this->_meta_box['id'] ) ) {
			$this->_meta_box['id'] = sanitize_title( $this->_meta_box['title'] );
		}

		add_action( 'dbx_post_advanced', array( &$this, 'init_fields_for_post' ) );
		add_action( 'cmb_init_fields', array( &$this, 'init_fields' ) );

		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save_for_post' ) );
		add_action( 'edit_attachment', array( &$this, 'save_for_post' ) );
		add_action( 'cmb_save_fields', array( &$this, 'save' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'wp_ajax_cmb_post_select', array( $this, 'cmb_ajax_post_select' ) );

		// Default filters for whether to show a metabox block or not.
		add_filter( 'cmb_is_metabox_displayed', array( $this, 'add_for_id' ), 2, 2 );
		add_filter( 'cmb_is_metabox_displayed', array( $this, 'hide_for_id' ), 3, 2 );
		add_filter( 'cmb_is_metabox_displayed', array( $this, 'add_for_page_template' ), 4, 2 );
		add_filter( 'cmb_is_metabox_displayed', array( $this, 'hide_for_page_template' ), 5, 2 );
		add_filter( 'cmb_is_metabox_displayed', array( $this, 'check_capabilities' ), 5, 2 );
	}

	/**
	 * Initialize metabox box fields.
	 *
	 * @uses cmb_init_fields
	 *
	 * @param int $post_id Optional. Post ID.
	 */
	public function init_fields( $post_id = 0 ) {

		foreach ( $this->_meta_box['fields'] as $key => $field ) {
			$values = array();

			$args = $field;
			unset( $args['id'] );
			unset( $args['type'] );
			unset( $args['name'] );

			$class  = _cmb_field_class_for_type( $field['type'] );
			$single = ( ! isset( $field['repeatable'] ) || false === $field['repeatable'] );

			// If we are on a post edit screen - get metadata value of the field for this post.
			if ( $post_id ) {
				$values = (array) get_post_meta( $post_id, $field['id'], $single );
			}

			/**
			 * Filter which fields are consdered to be "group" types.
			 *
			 * This is useful if you want to extend the group field for your own use and still
			 * use the array mapping below.
			 *
			 * @param array $group_fields Group field types
			 */
			$group_field_types = apply_filters( 'cmb_group_field_types', array( 'group' ) );

			// Handle repeatable values for group fields.
			if ( in_array( $field['type'], $group_field_types ) && $single ) {
				$values = array( $values );
			}

			if ( class_exists( $class ) ) {
				$field = new $class( $field['id'], $field['name'], (array) $values, $args );
				if ( $field->is_displayed( $post_id ) ) {
					$this->fields[] = $field;
				}
			}
		}

	}

	/**
	 * Initialize fields during the metabox loading process.
	 *
	 * @global int $post
	 *
	 * @uses dbx_post_advanced
	 *
	 * @return bool false if post ID fails or is on wrong screen.
	 */
	public function init_fields_for_post() {

		global $post;
		$post_id = null;

		// Get the current ID.
		if ( isset( $_GET['post'] ) ) {
			$post_id = wp_unslash( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$post_id = wp_unslash( $_POST['post_ID'] );
		} elseif ( ! empty( $post->ID ) ) {
			$post_id = $post->ID;
		}

		if ( is_page() || ! isset( $post_id ) ) {
			return false;
		}

		if ( ! is_numeric( $post_id ) || $post_id != floor( $post_id ) ) {
			return false;
		}

		$this->init_fields( (int) $post_id );

	}

	/**
	 * Load JS scripts in the admin area for plugin use.
	 *
	 * @uses admin_enqueue_scripts
	 */
	function enqueue_scripts() {

		if ( ! wp_script_is( 'cmb-scripts' ) ) {

			wp_enqueue_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ), CMB_VERSION );

			wp_localize_script( 'cmb-scripts', 'CMBData', array(
				'strings' => array(
					'confirmDeleteField' => esc_html__( 'Are you sure you want to delete this field?', 'cmb' ),
				),
			) );
		}

		foreach ( $this->fields as $field ) {
			$field->enqueue_scripts();
		}

	}

	/**
	 * Load stylesheets in admin area for plugin use.
	 *
	 * @uses admin_enqueue_styles
	 */
	function enqueue_styles() {

		$suffix = CMB_DEV ? '' : '.min';

		if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) ) {
			wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . "css/dist/cmb$suffix.css", array(), CMB_VERSION );
		} else {
			wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . 'css/legacy.css', array(), CMB_VERSION );
		}

		foreach ( $this->fields as $field ) {
			$field->enqueue_styles();
		}

	}

	/**
	 * Add a metabox collection.
	 *
	 * Parses a field collection for display attributes and runs the WP core functionality
	 * to register the metabox.
	 *
	 * @uses admin_menu
	 */
	function add() {

		$this->_meta_box['context'] = empty( $this->_meta_box['context'] ) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty( $this->_meta_box['priority'] ) ? 'low' : $this->_meta_box['priority'];

		// Backwards compatablilty.
		if ( isset( $this->_meta_box['show_on']['key'] ) ) {
			$this->_meta_box['show_on'][ $this->_meta_box['show_on']['key'] ] = $this->_meta_box['show_on']['value'];
			unset( $this->_meta_box['show_on']['key'] );
			unset( $this->_meta_box['show_on']['value'] );
		}

		foreach ( (array) $this->_meta_box['pages'] as $page ) {
			if ( $this->is_metabox_displayed() ) {
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array( &$this, 'show' ), $page, $this->_meta_box['context'], $this->_meta_box['priority'] );
			}
		}

	}

	/**
	 * Handle 'Show On' and 'Hide On' Filters.
	 *
	 * Runs checks to see if there are specific compatibilities or incompatibilities for displaying
	 * a CMB field collection.
	 */
	function is_metabox_displayed() {

		/**
		 * Filter whether a metabox should be displayed or not.
		 *
		 * @param bool $is_displayed Current status of display
		 * @param array $metabox Metabox information
		 */
		return apply_filters( 'cmb_is_metabox_displayed', true, $this->_meta_box );
	}

	/**
	 * Display CMB collection for particular post ID.
	 *
	 * Only works for field collections that have the 'show_on' attribute of 'id'.
	 *
	 * @param bool  $display Current display status.
	 * @param array $field Field arguments.
	 * @return bool (Potentially) modified display status
	 */
	function add_for_id( $display, $field = array() ) {

		if ( empty( $field ) ) {
			$field = $this->_meta_box;
		}

		if ( ! isset( $field['show_on']['id'] ) ) {
			return $display;
		}

		// Don't show CMB if we can't identify ID of a post.
		$post_id = $this->get_post_id();

		if ( ! isset( $post_id ) ) {
			return false;
		}

		// If value isn't an array, turn it into one.
		$field['show_on']['id'] = ! is_array( $field['show_on']['id'] ) ? array( $field['show_on']['id'] ) : $field['show_on']['id'];

		return in_array( $post_id, $field['show_on']['id'] );

	}

	/**
	 * Hide CMB collection for particular post ID.
	 *
	 * Only works for field collections that have the 'hide_on' attribute of 'id'.
	 *
	 * @param bool  $display Current display status.
	 * @param array $field Field arguments.
	 * @return bool (Potentially) modified display status
	 */
	function hide_for_id( $display, $field = array() ) {

		if ( empty( $field ) ) {
			$field = $this->_meta_box;
		}

		if ( ! isset( $field['hide_on']['id'] ) ) {
			return $display;
		}

		// Return if we can't identify ID of a post.
		$post_id = $this->get_post_id();
		if ( ! isset( $post_id ) ) {
			return $display;
		}

		// If value isn't an array, turn it into one.
		$field['hide_on']['id'] = ! is_array( $field['hide_on']['id'] ) ? array( $field['hide_on']['id'] ) : $field['hide_on']['id'];

		return ! in_array( $post_id, $field['hide_on']['id'] );

	}

	/**
	 * Display CMB collection on pages that have a particular page template assigned.
	 *
	 * Only works for field collections that have the 'show_on' attribute of 'page-template'.
	 *
	 * @param bool  $display Current display status.
	 * @param array $field Field arguments.
	 * @return bool (Potentially) modified display status
	 */
	function add_for_page_template( $display, $field = array() ) {

		if ( empty( $field ) ) {
			$field = $this->_meta_box;
		}

		if ( ! isset( $field['show_on']['page-template'] ) ) {
			return $display;
		}

		// Return false if we can't identify ID of a post.
		$post_id = $this->get_post_id();
		if ( ! isset( $post_id ) ) {
			return false;
		}

		// Get current template.
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );

		// If value isn't an array, turn it into one.
		$field['show_on']['page-template'] = ! is_array( $field['show_on']['page-template'] ) ? array( $field['show_on']['page-template'] ) : $field['show_on']['page-template'];

		return in_array( $current_template, $field['show_on']['page-template'] );

	}

	/**
	 * Hide CMB collection on pages that have a particular page template assigned.
	 *
	 * Only works for field collections that have the 'hide_on' attribute of 'page-template'.
	 *
	 * @param bool  $display Current display status.
	 * @param array $field Field arguments.
	 * @return bool (Potentially) modified display status
	 */
	function hide_for_page_template( $display, $field = array() ) {

		if ( empty( $field ) ) {
			$field = $this->_meta_box;
		}

		if ( ! isset( $field['hide_on']['page-template'] ) ) {
			return $display;
		}

		// Return $display if we can't identify ID of a post and hence its current template.
		$post_id = $this->get_post_id();

		if ( ! isset( $post_id ) ) {
			return $display;
		}

		// Get current template.
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );

		// If value isn't an array, turn it into one.
		$field['hide_on']['page-template'] = ! is_array( $field['hide_on']['page-template'] ) ? array( $field['hide_on']['page-template'] ) : $field['hide_on']['page-template'];

		return ! in_array( $current_template, $field['hide_on']['page-template'] );

	}

	/**
	 * Check capabilities of current user before displaying a CMB block.
	 *
	 * Only works for field collections that have the 'capability' attribute set.
	 *
	 * @param bool  $display Current display status.
	 * @param array $field Field arguments.
	 * @return bool (Potentially) modified display status
	 */
	function check_capabilities( $display, $field = array()  ) {

		if ( ! isset( $this->_meta_box['capability'] ) ) {
			return $display;
		}

		return current_user_can( $this->_meta_box['capability'] );

	}

	/**
	 * Print out field collection description.
	 */
	function description() {

		if ( ! empty( $this->_meta_box['desc'] ) ) { ?>

			<div class="cmb_metabox_description">
				<?php echo wp_kses_post( $this->_meta_box['desc'] ); ?>
			</div>

		<?php }

	}

	/**
	 * Display fields for a collection.
	 */
	function show() {

		$this->description(); ?>

		<input type="hidden" name="wp_meta_box_nonce" value="<?php esc_attr_e( wp_create_nonce( basename( __FILE__ ) ) ); ?>" />

		<?php self::layout_fields( $this->fields );

	}

	/**
	 * Layout an array of fields, depending on their 'cols' property.
	 *
	 * This is a static method so other fields can use it that rely on sub fields.
	 *
	 * @param array $fields Fields in a collection.
	 */
	static function layout_fields( array $fields ) {
		?>
		<div class="cmb_metabox">

			<?php $current_colspan = 0;

			foreach ( $fields as $field ) :

				if ( 0 == $current_colspan && ! $field instanceof CMB_Hidden_Field ) : ?>

					<div class="cmb-row">

				<?php endif;

				$current_colspan += $field->args['cols'];

				$classes = array( 'field', get_class( $field ) );

				if ( ! empty( $field->args['repeatable'] ) ) {
					$classes[] = 'repeatable';
				}

				if ( ! empty( $field->args['sortable'] ) && ! empty( $field->args['repeatable'] ) ) {
					$classes[] = 'cmb-sortable';
				} elseif ( ! empty( $field->args['sortable'] ) && empty( $field->args['repeatable'] ) ) {
					// Throw an error if calling the wrong combination of sortable and repeatable.
					_doing_it_wrong( 'cmb_meta_boxes', __( 'Calling sortable on a non-repeatable field. A field cannot be sortable without being repeatable.', 'cmb' ), 4.7 );
				}

				// Assign extra class for has label or has no label.
				if ( ! empty( $field->title ) ) {
					$label_designation = 'cmb-has-label';
				} else {
					$label_designation = 'cmb-no-label';
				}

				$attrs = array(
					sprintf( 'id="%s"', sanitize_html_class( $field->id ) ),
					sprintf( 'class="%s"', esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ) ),
				);

				// Field Repeatable Max.
				if ( isset( $field->args['repeatable_max'] ) ) {
					$attrs[] = sprintf( 'data-rep-max="%s"', intval( $field->args['repeatable_max'] ) );
				}

				// Ask for confirmation before removing field.
				if ( isset( $field->args['confirm_delete'] ) ) {
					$attrs[] = sprintf( 'data-confirm-delete="%s"', $field->args['confirm_delete'] ? 'true' : 'false' );
				}
				?>

				<div class="cmb-cell-<?php echo intval( $field->args['cols'] ); ?> <?php echo esc_attr( $label_designation ); ?>">

						<div <?php echo implode( ' ', $attrs ); ?>>
							<?php $field->display(); ?>
						</div>

						<input type="hidden" name="_cmb_present_<?php esc_attr_e( $field->id ); ?>" value="1" />

				</div>

				<?php if ( ( 12 == $current_colspan || $field === end( $fields ) ) && ! $field instanceof CMB_Hidden_Field ) :

					$current_colspan = 0; ?>

					</div><!-- .cmb-row -->

				<?php endif; ?>

			<?php endforeach; ?>

		</div>

	<?php }

	/**
	 * Remove unwanted hidden field values recursively.
	 *
	 * @param array $values Field value(s).
	 * @return array mixed Cleaned value(s)
	 */
	function strip_repeatable( $values ) {

		foreach ( $values as $key => $value ) {
			if ( false !== strpos( $key, 'cmb-group-x' ) || false !== strpos( $key, 'cmb-field-x' ) ) {
				unset( $values[ $key ] );
			} elseif ( is_array( $value ) ) {
				$values[ $key ] = $this->strip_repeatable( $value );
			}
		}

		return $values;
	}

	/**
	 * Save data from metabox.
	 *
	 * @uses cmb_save_fields
	 *
	 * @param int $post_id Optional. Post ID.
	 * @return int Post ID if nonce is not verified.
	 */
	function save( $post_id = 0 ) {

		// Verify nonce.
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		// Verify this meta box is for the right post type
		if ( ! in_array( get_post_type( $post_id ), (array) $this->_meta_box['pages'], true ) ) {
			return $post_id;
		}

		foreach ( $this->_meta_box['fields'] as $field ) {

			// Verify this meta box was shown on the page.
			if ( ! isset( $_POST[ '_cmb_present_' . $field['id'] ] ) ) {
				continue;
			}

			if ( isset( $_POST[ $field['id'] ] ) ) {
				$value = (array) $_POST[ $field['id'] ];
			} else {
				$value = array();
			}

			$value = $this->strip_repeatable( $value );

			if ( ! $class = _cmb_field_class_for_type( $field['type'] ) ) {
				do_action( 'cmb_save_' . $field['type'], $field, $value );
			}

			$field_obj = new $class( $field['id'], $field['name'], $value, $field );

			$field_obj->save( $post_id, $value );

		}

		// If we are not on a post, need to refresh the field objects to reflect new values, as we do not get a redirect.
		if ( ! $post_id ) {
			$this->fields = array();
			$this->init_fields();
		}
	}

	/**
	 * Trigger a save the on save_post hook.
	 *
	 * @uses save_post, edit_attachment
	 *
	 * @param int $post_id Post ID.
	 * @return int Post ID if field is autosaving.
	 */
	function save_for_post( $post_id ) {

		// Check if we're doing an autosave. Skip if so.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$this->save( $post_id );

	}

	/**
	 * Get a post ID for use when populating a metabox bo.
	 *
	 * @return int|null Post ID or null if missing GET variable.
	 */
	function get_post_id() {

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : null;

		if ( ! $post_id && isset( $_POST['post_id'] ) ) {
			$post_id = absint( $_POST['post_id'] );
		}

		return (int) $post_id;

	}

	/**
	 * AJAX callback for select fields.
	 */
	public function cmb_ajax_post_select() {

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
}
