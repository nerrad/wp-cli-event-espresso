<?php

namespace Nerrad\WPCLI\EE\interfaces;


/**
 * Interface ScaffoldCommandInterface
 * Should be implemented by any components that have commands for scaffolds.
 *
 * @package Nerrad\WPCLI\EE\interfaces
 * @subpackage
 * @author  Darren Ethier
 * @since   1.0.0
 */
interface ScaffoldCommandInterface extends CommandInterface
{

    /**
     * This is what will be used as the registered callback for a scaffold component command.
     * @return mixed
     */
    public function scaffoldCommand($args, array $assoc_args = array());
}