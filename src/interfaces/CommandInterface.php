<?php

namespace Nerrad\WPCLI\EE\interfaces;

/**
 * Interface CommandInterface
 * Any component implementing a command (or main command) should implement this interface.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author     Darren Ethier
 * @since      1.0.0
 */
interface CommandInterface
{

    /**
     * Return the entire document argument that is used as the third argument when registering a command.
     *
     * @return array
     */
    function commandDocumentArgument();


    /**
     * A short description for the command.
     *
     * @return string
     */
    function commandShortDescription();


    /**
     * Return the synopsis array which is an array of various descriptive properties for the command.
     *
     * @see  wp cli cookbook (link) for example format of the synopsis arguments.
     * @link https://make.wordpress.org/cli/handbook/commands-cookbook/#wp_cliadd_commands-third-args-parameter
     * @param bool $skip_global  This indicates whether the synopsis returned should include any attributes that a parent
     *                           command might already have.
     * @return array
     */
    function commandSynopsis($skip_global = true);
}