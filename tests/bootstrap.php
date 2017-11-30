<?php

// On Chassis, tests can silently fail, so introduce a shutdown function to print the last error.
// Throwing an exception sends a non-zero exit code.
register_shutdown_function( function() {
	// Only load in Chassis.
	if ( ! defined( 'WP_LOCAL_DEV' ) ) {
		return;
	}

	$error = error_get_last();
	if ( $error && isset( $error['message'] ) && ! defined( 'DOING_AJAX' ) ) {
		throw new Exception( $error['message'] );
	}
} );

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../custom-meta-boxes.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

// Include our test class.
require_once __DIR__ . '/class-test-field-case.php';

/**
 * Call protected/private method of a class.
 *
 * @param object &$object Instantiated object that we will run method on.
 * @param string $method_name Method name to call
 * @param array $parameters Array of parameters to pass into method.
 *
 * @return mixed Method return.
 */
function hmcmb_invoke_method( &$object, $method_name, array $parameters = array() ) {
	$reflection = new \ReflectionClass( get_class( $object ) );
	$method     = $reflection->getMethod( $method_name );
	$method->setAccessible( true );

	return $method->invokeArgs( $object, $parameters );
}