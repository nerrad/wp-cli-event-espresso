<?php
use Nerrad\WPCLI\EE\Loader;
use Nerrad\WPCLI\EE\services\utils\Locations;
if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}
//initialize basePath.
Locations::basePath(dirname(__FILE__));
$loader = new Loader();
$loader->addCommands();
