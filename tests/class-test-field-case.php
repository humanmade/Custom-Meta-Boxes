<?php
/**
 * Class for testing a field case
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use WP_UnitTest_Factory;
use WP_UnitTestCase;

/**
 * Class TestFieldCase
 */
abstract class TestFieldCase extends WP_UnitTestCase {
	/**
	 * Consistent post for use with testing fields.
	 *
	 * @var
	 */
	protected static $post;

	/**
	 * Class instance.
	 */
	//protected $instance;

	/**
	 * Setup objects for our tests.
	 *
	 * @param $factory
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		self::$post = $factory->post->create_and_get( [
			'post_author'  => 1,
			'post_status'  => 'publish',
			'post_content' => rand_str(),
			'post_title'   => rand_str(),
			'post_type'    => 'post',
		] );
	}

	public static function wpTearDownAfterClass() {
		wp_delete_post( self::$post->ID );
	}

	/**
	 * Verify that we can register and load a field without error.
	 */
	public function register_field( $field_id ) {}

	public function instantiate_class( $class_name ) {}

	abstract public function test_field_class_instantiation();
	abstract public function test_field_registration();
	abstract public function test_field_output();
}