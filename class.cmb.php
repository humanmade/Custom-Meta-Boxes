<?php

abstract class CMB {

	public $args;
	public $_object_id;
	private $_fields = array();

	protected $meta_box_defaults = array(
		'id'              => '',
		'title'           => '',
		'fields'          => array(),
		'show_on'         => array(),
		'capability'      => null, // Capability requried to show meta box.
		'capability_args' => null, // Args passed to current user can. If null, $this->_object_id is used.
		'layout_style'    => 'horizontal'
	);

	function __construct( $meta_box ) {

		global $pagenow;

		$this->args = wp_parse_args( $meta_box, $this->meta_box_defaults );

		if ( empty( $this->args['id'] ) )
			$this->args['id'] = sanitize_title( $this->args['title'] );

	}

	/**
	 * Init meta box for object ID.
	 *
	 * @param  int $object_id
	 * @return null
	 */
	public function init( $object_id ) {

		$this->_object_id = $object_id;

		$this->setup_hooks();

		$this->init_fields( $this->args['fields'] );

	}

	/**
	 * All actions and filters.
	 * This function is called by the init method.
	 *
	 * @return null
	 */
	public function setup_hooks() {

		// Load CMB Scripts.
		if ( $this->is_displayed() ) {
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		}

	}

	/**
	 * Whether the meta box should be shown or not.
	 *
	 * If false, the box is not displayed at all.
	 * Sub-classes of CMB can provide their own conditions
	 * although they should remember to call the parent method
	 *
	 * @return boolean
	 */
	public function is_displayed() {

		if ( $this->args['capability'] ) {

			if ( ! $this->args['capability_args'] ) {
				$this->args['capability_args'] = $this->_object_id;
			}

			return current_user_can(
				$this->args['capability'],
				$this->args['capability_args']
			);

		}

		return true;
	}

	/**
	 * Enqueue scripts and styles.
	 */
	function enqueue_scripts() {

		$suffix = CMB_DEV ? '' : '.min';

		// Load Main CMB script file
		wp_enqueue_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		// Load individual field scripts.
		foreach ( $this->_fields as $field )
			$field->enqueue_scripts();

		wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . "css/dist/cmb$suffix.css" );

		// Load individual field styles.
		foreach ( $this->_fields as $field )
			$field->enqueue_styles();

	}

	/**
	 * Initialize Fields
	 * @param [type] $fields [description]
	 */
	public function init_fields( $fields ) {

		if ( ! $this->is_displayed() ) {
			return;
		}

		foreach ( $fields as $key => $field ) {

			$values = array();

			$field = wp_parse_args(
				$field,
				array(
					'name' => '',
					'desc' => '',
					'type'  => '',
					'cols' => 12
				)
			);

			$class = _cmb_field_class_for_type( $field['type'] );

			if ( $class ) {
				$values = (array) $this->get_data( $this->_object_id, $field['id'] );
				$this->add_field( new $class( $field['id'], $field['name'], $values, $field ) );
			}

		}

	}

	public function add_field( CMB_Field $field ) {
		$this->_fields[] = $field;
	}

	public function &get_fields() {
		return $this->_fields;
	}

	/**
	 * Retreive field values from data store
	 * This does nothing. Subclasses should provide their own methods for saving data.
	 *
	 * @param  int $object_id
	 * @param  string $field_id
	 * @return array
	 */
	public function get_data( $object_id, $field_id ) {}

	/**
	 * Save field values
	 * This does nothing. Subclasses should provide their own methods for saving data.
	 *
	 * @param  int $object_id
	 * @param  string $field_id
	 * @return array
	 */
	public function save_data( $object_id, $field_id, $values ) {}

	/**
	 * Meta box output.
	 *
	 * Output the CMB structural markup.
	 * Loops through each field and displays the field.
	 *
	 * @return null
	 */
	function display() {

		$fields = $this->get_fields();
		$count  = count( $fields );
		$col    = 0;

		?>

		<div class="cmb-fields <?php echo esc_attr( 'cmb-fields-' . $this->args['layout_style'] ); ?>">

			<?php

			foreach ( $fields as $i => $field ) {

				// Start row.
				if ( $col == 0 )
					echo '<div class="cmb-row">';

				$this->display_field( $field );

				$col += $field->args['cols'];

				// End row. Make sure we close div if this is the last field.
				if ( $col == 12 || ( $i + 1 ) == $count )
					echo '</div>';

				if ( $col >= 12 )
					$col = 0;

			}

			?>

		</div>

		<input type="hidden" name="wp_meta_box_nonce" value="<?php esc_attr_e( wp_create_nonce( basename(__FILE__) ) ); ?>" />

		<?php

	}

	/**
	 * Field output.
	 *
	 * Outputs CMB field container markup & _cmb_present_$id field.
	 * Calls the display method on the field object.
	 *
	 * @param $field CMB_Field
	 * @return null
	 */
	function display_field( CMB_Field $field ) {

		$classes = array( 'field', get_class($field) );

		if ( ! empty( $field->args['repeatable'] ) )
			$classes[] = 'repeatable';

		if ( ! empty( $field->args['sortable'] ) )
			$classes[] = 'cmb-sortable';

		$attrs = array(
			sprintf( 'id="%s"', sanitize_html_class( $field->id ) ),
			sprintf( 'class="%s"', esc_attr( implode(' ', array_map( 'sanitize_html_class', $classes ) ) ) )
		);

		// Field Repeatable Max.
		if ( isset( $field->args['repeatable_max']  ) )
			$attrs[] = sprintf( 'data-rep-max="%s"', intval( $field->args['repeatable_max'] ) );

		?>

		<div class="<?php printf( 'cmb-cell-%d', absint( $field->args['cols'] ) ); ?>">

			<div <?php echo implode( ' ', $attrs ); ?>>
				<?php $field->display(); ?>
			</div>

			<input type="hidden" name="_cmb_present_<?php echo esc_attr( $field->id ); ?>" value="1" />

		</div>

		<?php

	}

	/**
	 * Save.
	 * Verify if field should be saved,
	 * loop through each field and parse save values,
	 * then call save_data(); for each field (this will handle the actual saving of data)
	 *
	 * @param numeric $object_id
	 * @return null
	 */
	function save( $object_id, $data )  {

		// verify nonce
		if ( ! isset( $data['wp_meta_box_nonce'] ) || ! wp_verify_nonce( $data['wp_meta_box_nonce'], basename( __FILE__ ) ) )
			return $object_id;

		foreach ( $this->get_fields() as $field ) {

			// verify this meta box was shown on the page
			if ( ! isset( $data['_cmb_present_' . $field->id ] ) )
				continue;

			$values = ( isset( $data[ $field->id ] ) ) ? (array) $data[ $field->id ] : array();
			$values = $this->strip_repeatable( $values );

			$field->set_values( $values );
			$field->parse_save_values();

			$this->save_data( $object_id, $field->id, $field->get_values() );

		}

	}

	function strip_repeatable( $values ) {

		foreach ( $values as $key => $value ) {

			if ( false !== strpos( $key, 'cmb-group-x' ) || false !== strpos( $key, 'cmb-field-x' ) )
				unset( $values[$key] );

			elseif ( is_array( $value ) )
				$values[$key] = $this->strip_repeatable( $value );

		}

		return $values;

	}

}
