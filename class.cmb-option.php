<?php

/**
 * Custom Meta Boxes for option pages
 */
class CMB_Options extends CMB {

	public $slug;

	protected $cmb_options_defaults = array(
		'menu_page_type'      => 'submenu_page', // submenu_page or menu_page
		'submenu_page_parent' => 'options-general.php', // Page parent.  Required if menu_page_type is submenu_page
		'menu_page_icon_url'  => null, // Menu item icon url.  Required if menu_page_type is menu_page
		'menu_page_position'  => null, // Menu item position.  Required if menu_page_type is menu_page
		'capability'          => 'manage_options', // Menu item position.  Required if menu_page_type is menu_page
	);

	public function __construct( $args ) {

		$args = wp_parse_args( $args, $this->cmb_options_defaults );

		parent::__construct( $args );

		if ( ! $this->args['capability'] ) {
			$this->args['capability'] = 'manage_options';
		}

		$this->slug = sanitize_title( $args['title'] );

		add_action( 'admin_init', array( &$this, 'init_hook' ) );

		// Must not hook in on admin_init - https://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

	}

	public function init_hook() {
		$this->init( get_current_blog_id() );
	}

	public function setup_hooks() {

		if ( isset( $_POST['wp_meta_box_nonce'] ) && $this->is_displayed() ) {
			$this->save_hook();
		}

		parent::setup_hooks();

	}

	public function save_hook() {
		$this->save( $this->object_id, $_POST );
	}

	function display_hook() { ?>

		<div class="wrap">

			<h2><?php echo esc_html( $this->args['title'] ); ?></h2>
			<form action="options-general.php?page=<?php echo esc_attr( $this->slug ); ?>" method="POST" style="max-width: 800px;">

				<?php $this->display();  ?>

				<br />

				<input type="submit" class="button-primary" value="Save Settings">
			</form>

		</div>

		<?php

	}

	public function is_displayed() {

		global $pagenow;

		if ( $pagenow === 'options-general.php' && isset( $_GET['page'] ) ) {
			return parent::is_displayed();
		}

		return false;
	}

	public function admin_menu() {

		if ( $this->args['menu_page_type'] === 'submenu_page' ) {
			add_submenu_page( $this->args['submenu_page_parent'], $this->args['title'], $this->args['title'], $this->args['capability'], $this->slug, array( $this, 'display_hook' ) );
		} else {
			add_menu_page( $this->args['title'], $this->args['title'], $this->args['capability'], $this->slug, array(), $this->args['menu_page_icon_url'], $this->args['menu_page_position'] );
		}

	}

	public function get_data( $object_id, $field_id ) {

		return get_option( $field_id, array() );

	}

	public function save_data( $object_id, $field_id, $values ) {

		if ( empty( $values ) )
			delete_option( $field_id );
		else
			update_option( $field_id, $values );

	}

}