<?php
use Nerrad\WPCLI\EE\Commands\Loader;
if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}
$loader = new Loader();
$loader->addCommands();
