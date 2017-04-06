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



    /**
     * When templates have subdirectories that need created for holding generated templates then this method should
     * return instructions for what needs created. The expected format is an array where each "row"
     * represents the directory created.  Example:
     *
     * array(
     *  '/root/path' => 'admin',
     *  '/root/path/admin' => 'some-other-directory'
     * )
     *
     * So arrays are parsed in order indicating what directories should be checked for first and then created if
     * missing. In this example the file generator receiving this will:
     *
     * 1. Check if `/root/path/` exists. If it does, then it will check if `admin` exists in that path and create it if
     *      it doesn't.
     * 2. Check if `/root/path/admin` exists.  If it doesn't then it will abort.  If it does, then it will check if
     *      'some-other-directory' exists within that path and create it if it doesn't.  And so on.
     *
     * So the order of the elements in the array matters because if any path provided in the key doesn't exist
     * then the directory doesn't get created.
     *
     * @param string $base_directory  This serves as the base for all the paths and should be prepended to the first
     *                                element in each row.
     * @return array
     */
    public function subdirectories($base_directory);
}