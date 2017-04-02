<?php

namespace Nerrad\WPCLI\EE\traits;

use Nerrad\WPCLI\EE\entities\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\entities\components\ComponentString;
use Nerrad\WPCLI\EE\entities\AddonString;
use \Nerrad\WPCLI\EE\interfaces\FileGeneratorInterface;
use Nerrad\WPCLI\EE\services\file_generators\BaseFileGenerator;
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
     * @var FileGeneratorInterface[]
     */
    protected $file_generators;


    /**
     * @var ComponentString[]
     */
    protected $component_strings = array();


    /**
     * Used to keep track of whether this component has already been initialized.
     * @var bool
     */
    protected $initialized = false;

    public function initializeScaffold($data, AddonString $addon_string)
    {
        if (! $this->initialized) {
            $this->data         = $data;
            $this->addon_string = $addon_string;
            $this->setComponentStrings();
            foreach ($this->component_strings as $component_string) {
                $this->file_generators[] = $this->getFileGenerator($component_string);
            }
            $this->initialized = true;
        }
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


    /**
     * Returns the file generator for the component
     * @param \Nerrad\WPCLI\EE\entities\components\ComponentString $component_string
     * @return \Nerrad\WPCLI\EE\services\file_generators\BaseFileGenerator
     */
    public function getFileGenerator(ComponentString $component_string)
    {
        $force = cliUtils\get_flag_value($this->data, 'force', false);
        $base_namespace = '\\Nerrad\\WPCLI\\EE\\';
        $file_generator_class = $base_namespace . 'services\\file_generators\\'
                                . $this->camelizeComponentName() . 'FileGenerator';
        $template_arguments_class = $base_namespace . 'entities\\components\\'
                                    . $this->camelizeComponentName() . 'TemplateArguments';
        $base_template_arguments = new AddonBaseTemplateArguments(
            $this->addon_string,
            $this->data,
            $force
        );
        try {
            return new $file_generator_class(
                $this->addon_string,
                new $template_arguments_class(
                    $component_string,
                    $base_template_arguments,
                    $this->addon_string,
                    $this->data,
                    $force
                ),
                true
            );
        } catch (Exception $e) {
            //so there isn't a component specific file generator, so let's just load the base file generator
            try {
                return new BaseFileGenerator(
                    $this->addon_string,
                    new $template_arguments_class(
                        $component_string,
                        $base_template_arguments,
                        $this->addon_string,
                        $this->data,
                        $force
                    ),
                    $force,
                    true
                );
            } catch (Exception $e) {
                //still NO file generator?  Okay let's throw the error now.
                WP_CLI::error(
                    sprintf(
                        'Unable to create the file generator because: %s',
                        $e->getMessage()
                    )
                );
            }
        }
        return null;
    }


    public function camelizeComponentName()
    {
        str_replace(' ', '', ucwords(preg_replace('/[^A-Z^a-z^0-9]+/', ' ', $this->componentName())));
    }



    /**
     * Return the entire document argument that is used as the third argument when registering a command.
     *
     * @return array
     */
    function commandDocumentArgument()
    {
        return array(
            'shortdesc' => $this->commandShortDescription(),
            'synopsis' => $this->commandSynopsis()
        );
    }
}