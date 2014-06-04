<?php

/**
 * Custom Meta Boxes for taxonomies
 */
class CMB_Taxonomies extends CMB {

    public $slug;

    protected $cmb_options_defaults = array(
        'taxonomy' => 'category',
        'show_on' => array('create', 'edit'),
        'priority' => 5
    );

    public function __construct( $args ) {

        $args = wp_parse_args( $args, $this->cmb_options_defaults );

        parent::__construct( $args );

        add_action( 'admin_init', array( &$this, 'init_hook' ) );

    }

    public function init_hook() {

        global $pagenow;
        $object_id = null;

        if ( $pagenow === 'edit-tags.php' && isset( $_REQUEST['tag_ID'] ) ) {
            $object_id = $_REQUEST['tag_ID'];
        }

        $this->init( $object_id );
    }


    public function setup_hooks() {

        if(empty($this->args['taxonomy']))
            return;

        $taxonomies = (array) $this->args['taxonomy'];

        foreach($taxonomies as $taxonomy) {

            if( in_array('create', $this->args['show_on']) && $this->is_displayed() ) {
                add_action( "{$taxonomy}_add_form_fields", array( &$this, 'display_hook' ), $this->args['priority'] );
            }

            if( in_array('edit', $this->args['show_on']) && $this->is_displayed() ) {
                add_action( "{$taxonomy}_edit_form_fields", array( &$this, 'display_hook' ), $this->args['priority'] );
            }

            add_action( "created_{$taxonomy}", array( &$this, 'save_hook' ), $this->args['priority'] );
            add_action( "edited_{$taxonomy}", array( &$this, 'save_hook' ), $this->args['priority'] );
        }

        parent::setup_hooks();
    }

    public function save_hook( $term_id ) {
        $this->save( $term_id, $_POST );
    }

    function display_hook( $term ) {

        if(empty($term->ID)) {
            $id = null;
        } else {
            $id = $term->ID;
        }

        printf( '<h3>%s</h3>', esc_html( $this->args['title'] ) );
        $this->display($id);
    }

    public function is_displayed() {

        global $pagenow;

        if ( $pagenow === 'edit-tags.php' && isset( $_REQUEST['taxonomy'] ) ) {
            return parent::is_displayed();
        }

        return false;
    }

    public function get_data( $term_id, $field_id ) {

        $term_meta = get_option( "taxonomy_$term_id" );

        if(!empty($term_meta[$field_id])) {
            return $term_meta[$field_id];
        }
    }

    public function save_data( $term_id, $field_id, $values ) {

        $term_meta = get_option( "taxonomy_$term_id" );

        if(!empty($values)) {
            $term_meta[$field_id] = $values;
        }

        update_option( "taxonomy_$term_id", $term_meta );
    }
}
