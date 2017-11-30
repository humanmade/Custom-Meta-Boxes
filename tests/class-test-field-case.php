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
	protected $instance;

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
	 * Update the argument set on a field class.
	 *
	 * @param $new_arguments
	 */
	public function update_arguments( $new_arguments ) {
		$this->instance->set_arguments( $new_arguments );
	}

	/**
	 * Verify that the field outputs with no errors for each argument set.
	 *
	 * @dataProvider argumentsProvider
	 */
	public function test_field_output( $arguments = [] ) {
		$this->update_arguments( $arguments );

		error_log( print_r( $arguments, true ) );

		// Check the default HTML output.
		// The point of these checks is to ensure that the field doesn't error out with each argument set.
		$this->expectOutputRegex( '/data-class=\"' . get_class( $this->instance ) . '\"/' );
		$this->instance->display();
	}

	/**
	 * Provide a set of arguments to test against.
	 *
	 * Here we provide a default set of arguments to test each field against.
	 *
	 * @return array Default argument set.
	 */
	public function argumentsProvider() {
		return [
			[],
			[
				'id' => 'my-ID',
			],
			[
				'id'   => 'my-ID',
				'name' => 'My Name'
			],
			[
				'id'   => 'my-ID',
				'name' => 'My Name',
				'desc' => 'A long description',
			],
			[
				'id'         => 'my-ID',
				'name'       => 'My Name',
				'desc'       => 'A long description',
				'repeatable' => true,
				'sortable'   => true,
			],
			[
				'id'         => 'my-ID',
				'name'       => 'My Name',
				'desc'       => 'A long description',
				'repeatable' => false,
				'sortable'   => true,
			],
			[
				'id'         => 'my-ID',
				'name'       => 'My Name',
				'desc'       => 'A long description',
				'repeatable' => true,
				'sortable'   => false,
			],
		];
	}
}