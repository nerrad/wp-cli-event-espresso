<?php

namespace Nerrad\WPCLI\EE\services\utils;

/**
 * Locations
 * A container holding all the paths for the command package.
 *
 * Typically Locations::basePath is called by the very first loaded file to ensure the base path is set correctly.
 *
 * @package Nerrad\WPCLI\EE\
 * @subpackage services\utils
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Locations
{
    /**
     * The base path for the command package.
     * @var string
     */
    private static $base_path;


    /**
     * The path to the templates for the command package
     * @var string
     */
    private static $templates_path;

    /**
     * Retrieves the base path for the command package
     * The first time this is called the provided path is set if the property is empty.
     * @param string $path
     * @return string
     */
    public static function basePath($path = '')
    {
        if (empty(self::$base_path)) {
            self::$base_path = $path;
        }
        return self::$base_path;
    }


    /**
     * Returns the path to the mustache templates used by scaffolds.
     * @return string
     */
    public static function templatesPath()
    {
        if (empty(self::$templates_path)) {
            self::$templates_path = self::$base_path . '/src/templates/';
        }
        return self::$templates_path;
    }

}