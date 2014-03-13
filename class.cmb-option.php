<?php

class CMB_Options extends CMB {

	public $object_id = 0;
	public $slug;

	protected $options_meta_box_defaults = array(
		'menu_page_type' => 'submenu_page', // submenu_page or menu_page
		'submenu_page_parent' => 'options-general.php', // Page parent.  Required if menu_page_type is submenu_page
		'menu_page_icon_url' => null, // Menu item icon url.  Required if menu_page_type is menu_page
		'menu_page_position' => null, // Menu item position.  Required if menu_page_type is menu_page
	);

	public function __construct( $meta_box ) {

		$this->slug = sanitize_title( $meta_box['title'] );

		parent::__construct( $meta_box );

		$this->_meta_box = wp_parse_args( $this->_meta_box, $this->options_meta_box_defaults );

		if ( ! $this->should_show_field() )
			return;

		add_action( 'admin_init', array( $this, 'init_hook' ) );
		add_action( 'admin_init', array( $this, 'save_hook' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	public function admin_menu() {

		if ( $this->_meta_box['menu_page_type'] === 'submenu_page' ) {
			add_submenu_page( $this->_meta_box['submenu_page_parent'], $this->_meta_box['title'], $this->_meta_box['title'], $this->_meta_box['capability'], $this->slug, array( $this, 'display_hook' ) );
		} else {
			add_menu_page( $this->_meta_box['title'], $this->_meta_box['title'], $this->_meta_box['capability'], $this->slug, array(), $this->_meta_box['menu_page_icon_url'], $this->_meta_box['menu_page_position'] );
		}

	}

	public function init_hook() {

		global $pagenow;

		if ( $pagenow === 'options-general.php' && isset( $_GET['page'] ) ) {
			$this->init( $this->object_id );
		}

	}

	public function save_hook() {

		$this->save( $this->object_id );

	}

	function display_hook() { ?>

		<div class="wrap">

			<h2><?php echo esc_html( $this->_meta_box['title'] ); ?></h2>
			<form action="options-general.php?page=<?php echo esc_attr( $this->slug ); ?>" method="POST" style="max-width: 800px;">

				<?php $this->display();  ?>

				<br />

				<input type="submit" class="button-primary" value="Save Settings">
			</form>

		</div>

		<?php

	}

	public function get_field_values( $object_id, $field_id ) {

		return get_option( $field_id, array() );

	}

	public function save_field_values( $object_id, $field_id, $values ) {

		if ( empty( $values ) )
			delete_option( $field_id );
		else
			update_option( $field_id, $values );

	}

}