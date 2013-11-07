<?php

abstract class CMB {
	
	public $_meta_box;
	public $_object_id;
	private $_fields = array();
	
	protected $meta_box_defaults = array(
		'id'     => '',
		'title'  => '',
		'fields' => array()
	);

	function __construct( $meta_box ) {

		global $pagenow;

		$this->_meta_box = wp_parse_args( $meta_box, $this->meta_box_defaults );

		if ( empty( $this->_meta_box['id'] ) )
			$this->_meta_box['id'] = sanitize_title( $this->_meta_box['title'] );

	}

	// public function hooks() {}

	public function init( $object_id ) {

		// Load CMB Scripts.
		add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );

		foreach ( $this->_meta_box['fields'] as $key => $field ) {
				
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
			
			$this->add_fields( new $class( $field, $values ) );

		}

	}

	function scripts() {

		wp_enqueue_script( 'cmb-scripts', trailingslashit( CMB_URL ) . 'js/cmb.js', array( 'jquery' ) );

		foreach ( $this->_fields as $field )
			$field->enqueue_scripts();

		wp_enqueue_style( 'cmb-styles', trailingslashit( CMB_URL ) . 'style.css' );

		foreach ( $this->_fields as $field )
			$field->enqueue_styles();

	}

	public function add_fields( CMB_Field $field ) {
		$this->_fields[] = $field;
	}

	public function &get_fields() {
		return $this->_fields;
	}

	public function get_field_values( $object_id, $field_id ) {}

	public function save_field_values( $object_id, $field_id, $values ) {}

	function display() {

		?>	
		
		<div class="cmb-fields">

			<?php foreach ( $this->get_fields() as $field ) : ?>
		
				<div class="cmb-row">
					<?php $this->display_field( $field ); ?>
				</div>
		
			<?php endforeach; ?>
		
		</div>

		<input type="hidden" name="wp_meta_box_nonce" value="<?php esc_attr_e( wp_create_nonce( basename(__FILE__) ) ); ?>" />
		
		<?php

	}

	function display_field( $field ) {

		$grid_class = sprintf( 'cmb-grid-%d', absint( $field->args['cols'] ) );

		$classes = array('cmb-field');

		if ( ! empty( $field->args['repeatable'] ) )
			$classes[] = 'repeatable';

		if ( ! empty( $field->args['sortable'] ) )
			$classes[] = 'cmb-sortable';

		$classes[] = get_class($field);

		$classes = 'class="' . esc_attr( implode(' ', array_map( 'sanitize_html_class', $classes ) ) ) . '"';

		$attrs = array();

		if ( isset( $field->args['repeatable_max']  ) )
			$attrs[] = 'data-rep-max="' . intval( $field->args['repeatable_max'] ) . '"';

		$attrs = implode( ' ', $attrs );

		?>

		<div class="cmb-grid <?php echo sanitize_html_class( $grid_class ); ?>">
			
			<div <?php echo $classes; ?> <?php echo $attrs; ?>>
				<?php $field->display(); ?>
			</div>
		
		</div>

		<input type="hidden" name="_cmb_present_<?php esc_attr_e( $field->id ); ?>" value="1" />

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
