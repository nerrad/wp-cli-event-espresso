<?php

namespace Nerrad\WPCLI\EE\interfaces;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\components\ComponentString;


/**
 * Interface ComponentHasScaffoldInterface
 * Any components that are providing scaffold information should implement this interface.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage interfaces
 * @author     Darren Ethier
 * @since      1.0.0
 */
interface ComponentHasScaffoldInterface extends initializeBeforeCommandInterface
{
    /**
     * Called when initializing the component for command execution.
     *
     * @param array $data       The arguments passed with the command
     * @return void
     */
    public function initializeScaffold(array $data, AddonString $addon_string);


    /**
     * Return an array of parts output to the registration array for this component when the addon is registered.
     *
     * @return array
     */
    public function registrationParts();


    /**
     * If this component has any autoloader paths needing registered with the 'autolaoder_paths' array in addon
     * registration it should get returned via this method.
     *
     * @return array
     */
    public function autoloaderPaths();


    /**
     * This should return any slugs being registered for the component.
     *
     * @return array
     */
    public function getSlugs();


    /**
     * Should return the file generator for the component.
     *
     * @return \Nerrad\WPCLI\EE\interfaces\FileGeneratorInterface
     */
    public function getFileGenerator(ComponentString $component_string);

}