<?php

namespace Nerrad\WPCLI\EE\interfaces;

/**
 * Interface BaseCommandInterface
 * Should be implemented by any base command classes (eg. AddonScaffold)
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author     Darren Ethier
 * @since      1.0.0
 */
interface BaseCommandInterface extends CommandInterface
{

    /**
     * This is the command that gets executed (and registered with WP_CLI as a command).
     *
     * @param       $args
     * @param array $assoc_args
     * @return mixed
     */
    public function executeCommand($args, array $assoc_args = array());


    /**
     * This is used to register the main command.
     */
    public function command();
}