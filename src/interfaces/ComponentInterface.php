<?php

namespace Nerrad\WPCLI\EE\interfaces;

use Nerrad\WPCLI\EE\entities\AddonBaseConstantString;
use Nerrad\WPCLI\EE\entities\AddonString;

/**
 * Interface ComponentInterface
 * Should be implemented by all components
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author     Darren Ethier
 * @since      1.0.0
 */
interface ComponentInterface
{
    /**
     * The name of the component. eg 'module' or 'message_type'.  Should correspond to the
     * argument key used by the related command.
     *
     * @return string
     */
    public function componentName();
}