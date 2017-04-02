<?php

namespace Nerrad\WPCLI\EE\interfaces;


/**
 * Interface TemplateArgumentsInterface
 * Implemented by all classes describing template arguments.
 *
 * @package     Nerrad\WPCLI\EE
 * @subpackage  interfaces
 * @author      Darren Ethier
 * @since       1.0.0
 */
interface TemplateArgumentsInterface
{
    /**
     * Converts all the properties to an array ready for the templates.
     */
    public function toArray();
}