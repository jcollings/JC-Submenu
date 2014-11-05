<?php
/**
 * Load WordPress test environment
 */

$path = '../../../../tests/phpunit/includes/bootstrap.php';

$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array("jc-submenu/submenu.php" ),
);

if( file_exists( $path ) ) {
    require_once $path;
} else {
    exit( "Couldn't find path to wordpress-tests/bootstrap.php\n" );
}