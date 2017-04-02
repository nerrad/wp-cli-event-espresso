<?php

namespace Nerrad\WPCLI\EE\traits;

use Nerrad\WPCLI\EE\entities\components\ComponentString;
use Nerrad\WPCLI\EE\entities\AddonString;
use \Nerrad\WPCLI\EE\interfaces\FileGeneratorInterface;
use WP_CLI\Utils as cliUtils;
use WP_CLI;
use Exception;

/**
 * ComponentScaffoldTrait
 * This trait is for any component scaffolds and should be implemented by all component scaffolds files
 *
 * @package Nerrad\WPCLI\EE\
 * @subpackage traits
 * @author  Darren Ethier
 * @since   1.0.0
 */
trait ComponentScaffoldTrait
{
    /**
     * The arguments coming in from the command
     *
     * @var array
     */
    protected $data = array();


    /**
     * @var AddonString
     */
    protected $addon_string;


    /**
     * @var string
     */
    protected $addon_directory;


    /**
     * @var FileGeneratorInterface
     */
    protected $file_generator;


    /**
     * @var ComponentString[]
     */
    protected $component_strings = array();

    public function initializeScaffold($data, AddonString $addon_string)
    {
        $this->data      = $data;
        $this->addon_string = $addon_string;
        $this->setComponentStrings();
        $this->file_generator = $this->getFileGenerator();
    }


    /**
     * Returns whatever slugs were sent in as a part of the argument array for the component.
     *
     * @return array
     */
    public function getSlugs()
    {
        $slugs = isset($this->data[$this->componentName()])
            ? explode(',', $this->data[$this->componentName()])
            : array();
        return empty($slugs) && isset($this->data['slugs'])
            ? explode(',', $this->data['slugs'])
            : array();
    }


    /**
     * Creates a ComponentString object for each component slugs provided by the component and assigns to the
     * component_strings property.
     * For example, admin_pages might have multiple "slugs" representing each admin page the user wants generated.
     */
    private function setComponentStrings()
    {
        $slugs = $this->getSlugs();
        array_walk($slugs, function ($slug) {
            $this->component_strings[] = new ComponentString($slug);
        });
    }


    public function getFileGenerator()
    {
        $force = cliUtils\get_flag_value($this->data, 'force', false);
        $base_namespace = '\\Nerrad\\WPCLI\\EE\\';
        $file_generator_class = $base_namespace . 'services\\file_generators\\'
                                . $this->componentName() . 'FileGenerator';
        $template_arguments_class = $base_namespace . 'entities\\template_arguments\\'
                                    . $this->componentName() . 'TemplateArguments';
        try {
            return new $file_generator_class(
                $this->addon_string,
                new $template_arguments_class($this->addon_string, $this->data, $force)
            );
        } catch (Exception $e) {
            WP_CLI::error(
                sprintf(
                    'Unable to create the file generator because: %s',
                    $e->getMessage()
                )
            );
        }
    }
}