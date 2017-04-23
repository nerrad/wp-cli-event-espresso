<?php

namespace Nerrad\WPCLI\EE\entities;

use WP_CLI\Utils as cliUtils;

/**
 * AddonScaffoldFlag
 * This is used for Scaffold type commands to parse represent flag arguments from the given $data (usually from the
 * arguments provided with the command execution)
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage entities
 * @author     Darren Ethier
 * @since      1.0.0
 */
class AddonScaffoldFlag
{
    /**
     * Whether or not to build the config scaffold
     *
     * @var bool
     */
    private $include_config = false;

    /**
     * Whether or not to build the tests scaffold.
     *
     * @var bool
     */
    private $skip_tests = false;


    /**
     * Whether or not to force overwrites on existing files when building scaffolds.
     *
     * @var bool
     */
    private $force = false;


    /**
     * AddonScaffoldFlag constructor.
     *
     * @param array $data This is any arguments provided when a command was invoked
     */
    public function __construct($data)
    {
        $this->include_config = cliUtils\get_flag_value($data, 'include_config', false);
        $this->skip_tests     = cliUtils\get_flag_value($data, 'skip_tests', false);
        $this->force          = cliUtils\get_flag_value($data, 'force', false);
    }

    /**
     * Whether or not to include the config component for the scaffold.
     *
     * @return bool
     */
    public function isIncludeConfig()
    {
        return $this->include_config;
    }

    /**
     * Whether or not to include the tests component for the scaffold.
     *
     * @return bool
     */
    public function isSkipTests()
    {
        return $this->skip_tests;
    }

    /**
     * Whether or not to overwrite existing files when generating new ones.
     *
     * @return bool
     */
    public function isForce()
    {
        return $this->force;
    }
}