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


    /**
     * Returns a mapped array of generated file names to file source mustache template.
     * @param string $addon_directory  The base addon directory where files will be installed.
     * @return array
     */
    public function templates($addon_directory);
}