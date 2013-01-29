<?php

/**
 * Create meta boxes
 */
class CMB_Meta_Box {

	protected $_meta_box;
	private $fields = array();

	function __construct( $meta_box ) {

		$this->_meta_box = $meta_box;

		if ( empty( $this->_meta_box['id'] ) )
			$this->_meta_box['id'] = $this->_meta_box['title'];

		$upload = false;

		foreach ( $meta_box['fields'] as $field ) {
			if ( $field['type'] == 'file' || $field['type'] == 'file_list' ) {
				$upload = true;
				break;
			}
		}
		
		add_action( 'dbx_post_advanced', array( &$this, 'init_fields_for_post' ) );
		add_action( 'cmb_init_fields', array( &$this, 'init_fields' ) );

		global $pagenow;
		if ( $upload && in_array( $pagenow, array( 'page.php', 'page-new.php', 'post.php', 'post-new.php' ) ) ) {
			add_action( 'admin_head', array( &$this, 'add_post_enctype' ) );
		}

		add_action( 'admin_menu', array( &$this, 'add' ) );
		add_action( 'save_post', array( &$this, 'save_for_post' ) );
		add_action( 'cmb_save_fields', array( &$this, 'save' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );

		add_filter( 'cmb_show_on', array( &$this, 'add_for_id' ), 10, 2 );
		add_filter( 'cmb_show_on', array( &$this, 'add_for_page_template' ), 10, 2 );
	}

	public function init_fields( $post_id = 0 ) {

		foreach ( $this->_meta_box['fields'] as $key => $field ) {

			$values = array();

			// Set up blank or default values for empty ones
			//
			$defaults = array(
				'name' => '',
				'desc' => '',
				'std'  => '',
				'cols' => 12
			);

			$field = wp_parse_args( $field, $defaults );

			if ( 'file' == $field['type'] && ! isset( $field['allow'] ) )
				$field['allow'] = array( 'url', 'attachment' );

			if ( 'file' == $field['type'] && ! isset( $field['save_id'] ) )
				$field['save_id']  = false;

			$field['name_attr'] = $field['id'];
			$class = _cmb_field_class_for_type( $field['type'] );

			if ( ! empty( $this->_meta_box['repeatable'] ) )
				$field['repeatable'] = true;


			//Else if we are on a post edit screen
			 elseif ( $post_id )
				$values = (array) get_post_meta( $post_id, $field['id'], false );


			$this->fields[] = new $class( $field['id'], $field['name'], (array) $values, $field );
		}

	}

	public function init_fields_for_post() {

		global $post, $temp_ID;

		// Get the current ID
		if( isset( $_GET['post'] ) ) 
			$post_id = $_GET['post'];
		
		elseif( isset( $_POST['post_ID'] ) ) 
			$post_id = $_POST['post_ID'];
		
		elseif ( ! empty( $post->ID ) )
			$post_id = $post->ID;

		if ( is_page() || ! isset( $post_id ) )
			return false;

		$this->init_fields( (int) $post_id );
	}

	function enqueue_scripts() {
		foreach ( $this->fields as $field ) {
			$field->enqueue_scripts();
		}
	}

	function enqueue_styles() {
		foreach ( $this->fields as $field ) {
			$field->enqueue_styles();
		}
	}

	function add_post_enctype() {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	// Add metaboxe
	function add() {
		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'low' : $this->_meta_box['priority'];
		$this->_meta_box['show_on'] = empty( $this->_meta_box['show_on'] ) ? array('key' => false, 'value' => false) : $this->_meta_box['show_on'];
		
		foreach ( (array) $this->_meta_box['pages'] as $page ) {
			if( apply_filters( 'cmb_show_on', true, $this->_meta_box ) )
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
		}
	}
	
	/**
	 * Show On Filters
	 * Use the 'cmb_show_on' filter to further refine the conditions under which a metabox is displayed.
	 * Below you can limit it by ID and page template
	 */
	 
	// Add for ID 
	function add_for_id( $display, $meta_box ) {
		if ( 'id' !== $meta_box['show_on']['key'] )
			return $display;

		// If we're showing it based on ID, get the current ID					
		if( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if( !isset( $post_id ) )
			return false;
		
		// If value isn't an array, turn it into one	
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];
		
		// If current page id is in the included array, display the metabox

		if ( in_array( $post_id, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
	}
	
	// Add for Page Template
	function add_for_page_template( $display, $meta_box ) {
		if( 'page-template' !== $meta_box['show_on']['key'] )
			return $display;
			
		// Get the current ID
		if( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if( !( isset( $post_id ) || is_page() ) ) return false;
			
		// Get current template
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );
		
		// If value isn't an array, turn it into one	
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		if( in_array( $current_template, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
	}
	
	// Show fields
	function show() {

		// Use nonce for verification
		echo '<input type="hidden" name="wp_meta_box_nonce" value="', wp_create_nonce( basename(__FILE__) ), '" />';

		self::layout_fields( $this->fields );
	}

	/**
	 * Layout an array of fields, depending on their 'cols' property. 
	 * 
	 * This is a static method so other fields can use it that rely on sub fields
	 * 
	 * @param  CMB_Field[]  $fields 
	 */
	static function layout_fields( array $fields ) {

		?>

		<table class="form-table cmb_metabox">

			<?php
			$current_colspan = 0;

			foreach ( $fields as $field ) :

				if ( $current_colspan == 0 ) :
					?>
					<tr>
				<?php endif;

				$current_colspan += $field->args['cols'];
				?>

				<td style="width: <?php echo $field->args['cols'] / 12 * 100 ?>%" colspan="<?php echo $field->args['cols'] ?>">
					<div class="field <?php echo !empty( $field->args['repeatable'] ) ? 'repeatable' : '' ?>">
						<?php $field->display(); ?>
					</div>
				</td>

				<?php if ( $current_colspan == 12 ) :
					$current_colspan = 0;
					?>
					</tr>
				<?php endif; ?>

				<input type="hidden" name="_cmb_present_<?php echo $field->id ?>" value="1" />
			<?php endforeach; ?>
		</table>

		<?php
	}

	// Save data from metabox
	function save( $post_id = 0 )  {

		// verify nonce
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
			return $post_id;
		}

		foreach ( $this->_meta_box['fields'] as $field ) {
			
			// verify this meta box was shown on the page
			if ( ! isset( $_POST['_cmb_present_' . $field['id'] ] ) )
				continue;

			if ( isset( $_POST[$field['id']] ) )
				$value = (array) $_POST[$field['id']];
			else
				$value = array();

			// if it's repeatable take off the last one
			if ( ! empty( $field['repeatable'] ) && $field['type'] != 'group' ) {
				end( $value );
				unset( $value[key( $value )] );
				reset( $value );
			}

			if ( ! $class = _cmb_field_class_for_type( $field['type'] ) ) {
				do_action('cmb_save_' . $field['type'], $field, $value);
			}

			if ( !empty(  $this->_meta_box['repeatable'] ) )
				$field['repeatable'] = true;
			
			$field_obj = new $class( $field['id'], $field['name'], $value, $field );
			$field_obj->save( $post_id, $value );
				
		}

		//If we are not on a post, need to refresh the field objects to reflect new values, as we do not get a redirect
		if ( ! $post_id ) {
			$this->fields = array();
			$this->init_fields();
		}
	}

	//Save the on save_post hook
	function save_for_post( $post_id ) {

		// check autosave
		if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$this->save( $post_id );
	}
}
