<?php

namespace Nerrad\WPCLI\EE\abstracts;

use Nerrad\WPCLI\EE\interfaces\BaseCommandInterface;
use Nerrad\WPCLI\EE\services\ComponentManager;

/**
 * CommandWithComponents
 * Implemented by any base commands that has components.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage abstracts
 * @author     Darren Ethier
 * @since      1.0.0
 */
abstract class CommandWithComponents implements BaseCommandInterface
{
    /**
     * @var ComponentManager
     */
    protected $component_manager;

    /**
     * CommandWithComponents constructor.
     *
     * @param \Nerrad\WPCLI\EE\services\ComponentManager $component_manager
     */
    public function __construct(ComponentManager $component_manager)
    {
        $this->component_manager = $component_manager;
    }
}