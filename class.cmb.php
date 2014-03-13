<?php

abstract class CMB {

	public $args;
	public $_object_id;
	private $_fields = array();

	protected $meta_box_defaults = array(
		'id'              => '',
		'title'           => '',
		'fields'          => array(),
		'capability'      => null,
		'capability_args' => null
	);

	function __construct( $meta_box ) {

		global $pagenow;

		$this->_meta_box = wp_parse_args( $meta_box, $this->meta_box_defaults );

		if ( empty( $this->_meta_box['id'] ) )
			$this->_meta_box['id'] = sanitize_title( $this->_meta_box['title'] );

	}

	// public function hooks() {}

	public function init( $object_id ) {

		if ( ! $this->should_show_field() )
			return;

		// Load CMB Scripts.
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

		foreach ( $this->args['fields'] as $key => $field ) {

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

			if ( ! $class )
				continue;

			$values = (array) $this->get_field_values( $object_id, $field['id'] );
			$this->add_field( new $class( $field['id'], $field['name'], $values, $field ) );

		}

	}

	protected function should_show_field() {

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

		// Load main CMB Style. (Load legacy pre WordPress 3.8)
		if ( version_compare( get_bloginfo( 'version' ), '3.8', '>=' ) )
			wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . "css/dist/cmb$suffix.css" );
		else
			wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . 'css/legacy.css' );

		// Load individual field styles.
		foreach ( $this->_fields as $field )
			$field->enqueue_styles();

	}

	public function add_field( CMB_Field $field ) {
		$this->_fields[] = $field;
	}

	public function &get_fields() {
		return $this->_fields;
	}

	public function get_field_values( $object_id, $field_id ) {}

	public function save_field_values( $object_id, $field_id, $values ) {}

	function display() {

		$fields = $this->get_fields();
		$count  = count( $fields );
		$col    = 0;

		?>

		<div class="cmb-fields cmb-fields-horizontal">

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

	function display_field( $field ) {

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

				<input type="hidden" name="_cmb_present_<?php esc_attr_e( $field->id ); ?>" value="1" />

		</div>

		<?php

	}

	function save( $object_id )  {

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename( __FILE__ ) ) )
			return $object_id;

		foreach ( $this->_fields as $field ) {

			// verify this meta box was shown on the page
			if ( ! isset( $_POST['_cmb_present_' . $field->id ] ) )
				continue;

			$values = ( isset( $_POST[ $field->id ] ) ) ? (array) $_POST[ $field->id ] : array();
			$values = $this->strip_repeatable( $values );

			$field->set_values( $values );
			$field->parse_save_values();

			$this->save_field_values( $object_id, $field->id, $field->get_values() );

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
