<?php

namespace Nerrad\WPCLI\EE\services\file_generators;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\template_arguments\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\interfaces\BaseFileGeneratorInterface;
use Nerrad\WPCLI\EE\traits\ScaffoldFiles;
use Nerrad\WPCLI\EE\abstracts\TemplateArgumentsAbstract;
use WP_CLI\Utils as cliUtils;

/**
 * BaseFileGenerator
 * This is used for generating the base file scaffolds.
 *
 * @package    Nerrad\WPCLI\EE\
 * @subpackage services\file_generators
 * @author     Darren Ethier
 * @since      1.0.0
 */
class BaseFileGenerator implements BaseFileGeneratorInterface
{
    use ScaffoldFiles;

    /**
     * @var AddonBaseTemplateArguments
     */
    private $template_arguments;


    /**
     * @var AddonString
     */
    private $addon_string;


    /**
     * @var string The directory the addon lives in.
     */
    private $directory;


    /**
     * BaseFileGenerator constructor.
     *
     * @param AddonString                $addon_string
     * @param TemplateArgumentsAbstract $template_arguments
     */
    public function __construct(AddonString $addon_string, TemplateArgumentsAbstract $template_arguments)
    {
        $this->addon_string       = $addon_string;
        $this->template_arguments = $template_arguments;
        $this->directory          = $this->getAddonDirectory('eea-' . $this->addon_string->slug());
    }


    /**
     * Responsible for writing the files for the scaffold.
     */
    public function writeFiles()
    {
        $template_data = $this->template_arguments->toArray();
        $files_written = $this->createFiles(
            array_map(
                function ($template_path) use ($template_data) {
                    cliUtils\mustache_render($template_path, $template_data);
                },
                $this->template_arguments->templates($this->directory)
            ),
            $this->template_arguments->isForce()
        );
        $this->logFilesWritten($files_written, 'Files for addon created.');
    }
}