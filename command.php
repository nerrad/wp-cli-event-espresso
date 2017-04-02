<?php
use Nerrad\WPCLI\EE\Loader;
use Nerrad\WPCLI\EE\services\utils\Locations;

$autoload = dirname( __FILE__ ) . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    require_once $autoload;
}

if (! class_exists( 'WP_CLI' ) ) {
	return;
}
//initialize basePath.
Locations::basePath(dirname(__FILE__));
$loader = new Loader();
$loader->addCommands();
