<?php

namespace Nerrad\WPCLI\EE\abstracts;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\interfaces\TemplateArgumentsInterface;
use WP_CLI\utils as cliUtils;

/**
 * TemplateArgumentsAbstract
 * Implemented by classes that build template arguments for a scaffold command file generator.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage abstracts
 * @author     Darren Ethier
 * @since      1.0.0
 */
abstract class TemplateArgumentsAbstract implements TemplateArgumentsInterface
{
    /**
     * @var AddonString
     */
    protected $addon_string;


    /**
     * Whether or not to force overwrites of existing files.
     *
     * @var bool
     */
    protected $force = false;


    /**
     * TemplateArgumentsAbstract constructor.
     *
     * @param \Nerrad\WPCLI\EE\entities\AddonString $addon_string
     * @param array                                 $data  Incoming data from command invoked.
     * @param bool                                  $force Whether to overwrite files or not.
     */
    public function __construct(AddonString $addon_string, $data, $force = false)
    {
        $this->force        = $force;
        $this->addon_string = $addon_string;
        $this->assignDataToProps($data);
    }

    /**
     * Takes care of parsing the incoming data and assigning to the correct props with appropriate defaults.
     * Child classes will usually override this for controlling defaults on their own properties.
     *
     * @param                                       $data
     */
    protected function assignDataToProps($data)
    {
        array_walk(array_keys(get_object_vars($this)), function ($property) use ($data) {
            if ($property === 'force'
                || $property === 'addon_string'
                || ! property_exists($this, $property)
            ) {
                return;
            }
            $this->{$property} = cliUtils\get_flag_value($data, $property, $this->{$property});
        });
    }


    /**
     * Return whether to force overwrites or not.
     * @return bool
     */
    public function isForce()
    {
        return $this->force;
    }
}