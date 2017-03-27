<?php

namespace Nerrad\WPCLI\EE\services;

use WP_CLI;
use Nerrad\WPCLI\EE\utils\Files;
use Nerrad\WPCLI\EE\abstracts\ComponentAbstract;
use Exception;


/**
 * The sole purpose of this class is to construct the options array that is injected into the
 * mustache template for the main addon file.
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage services
 * @author     Darren Ethier
 * @since      1.0.0
 */
class ComponentBuilder {

    /**
     * Found Components
     * @var ComponentAbstract[]
     */
    private $components = array();


    /**
     * Holds all the generated constants
     * @var array
     */
    private $constants = array();


    /**
     * Holds the incoming data from the command
     * @var array
     */
    private $data = array();


    /**
     * Holds the array of all the various registration parts used to generate the addon registration template string.
     * @var array
     */
    private $registration_parts = array();

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->buildBaseParts();
        $this->loadComponents();
    }


    /**
     * @return ComponentAbstract[]
     */
    public function components() {
        return $this->components;
    }


    /**
     * @param string $component_name
     * @return ComponentAbstract|null
     */
    public function component($component_name) {
        return isset($this->components[$component_name])
            ? $this->components[$component_name]
            : null;
    }


    private function buildBaseConstants()
    {
        //need the package to build the constants
        if (! $this->data['addon_package']) {
            WP_CLI::error(
                'Unable to build constants for the skeleton because there is no `$addon_package` defined.'
            );
        }

        $package_with_prefix = 'EE_' . $this->data['addon_package'] .  '_';

        $this->constants = array(
            'core_version_required' => $package_with_prefix . 'CORE_VERSION_REQUIRED',
            'version' => $package_with_prefix . 'VERSION',
            'plugin_file' => $package_with_prefix . 'PLUGIN_FILE',
            'basename' => $package_with_prefix . 'BASENAME',
            'path' => $package_with_prefix . 'PATH',
            'url' => $package_with_prefix . 'URL',
        );
    }


    /**
     * Construct the base components for the data.
     */
    private function buildBaseParts()
    {
        $this->buildBaseConstants();
        $this->registration_parts = array(
            "'version' => {$this->constants['version']}",
            "'plugin_slug' => {$this->data['addon_slug']}",
            "'min_core_version' => {$this->constants['core_version_required']}",
            "'main_file_path' => {$this->constants['plugin_file']}",
        );
    }


    private function loadComponents()
    {
        foreach (glob(dirname(__FILE__) . '/components/*') as $file) {
            try {
                $fqcn      = 'Nerrad\\WPCLI\\EE\\services\\components\\ ' . Files::getClassnameFromFilePath($file);
                $component = new $fqcn($this->data, $this->constants);
                //if component is an instance of ComponentAbstract then we don't set it on the component array.
                if ($component instanceof ComponentAbstract) {
                    $this->components[$component->componentName()] = $component;
                }
            } catch(Exception $e) {
                WP_CLI::error($e->getMessage());
            }
        }
    }


    public function generateAndReturnRegistrationPartsTemplateString()
    {
        $autoloader_part = $this->generateAutoloaderPart();
        if ($autoloader_part) {
            $this->registration_parts[] = $autoloader_part;
        }

        //get all the component parts!
        array_walk($this->components, function (ComponentAbstract $component) {
            if ($component->registrationParts()) {
                $this->registration_parts[] = $component->registrationParts();
            }
        });

        //k let's return the parts as the string for the template.
        return $this->formattedString($this->registration_parts);
    }

    private function generateAutoloaderPart()
    {
        $autoloader_paths = array();
        foreach ($this->components as $component) {
            $paths = (array) $component->autoloaderPaths();
            array_push($autoloader_paths, $paths);
        }
        if ($autoloader_paths) {
            return "'autoloader_paths' => " . $this->formattedString($autoloader_paths);
        }
        return '';
    }


    /**
     * Just returns a nice formatted string for the parts.
     * @param $parts
     * @return string
     */
    private function formattedString($parts) {
        return 'array('
               . PHP_EOL
               . "\t"
               . implode(',' . PHP_EOL . "\t", $parts)
               . PHP_EOL
               . ')';
    }

}