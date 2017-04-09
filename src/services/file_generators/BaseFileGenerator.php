<?php

namespace Nerrad\WPCLI\EE\services\file_generators;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\template_arguments\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\interfaces\BaseFileGeneratorInterface;
use Nerrad\WPCLI\EE\traits\ScaffoldFiles;
use Nerrad\WPCLI\EE\abstracts\BaseTemplateArgumentsAbstract;
use WP_CLI\Utils as cliUtils;
use WP_CLI;

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
     * @param AddonString                   $addon_string
     * @param BaseTemplateArgumentsAbstract $template_arguments
     * @param bool                          $verify                 Whether or not to verify the root addon directory exists.
     */
    public function __construct(
        AddonString $addon_string,
        BaseTemplateArgumentsAbstract $template_arguments,
        $verify = false
    ) {
        $this->addon_string       = $addon_string;
        $this->template_arguments = $template_arguments;
        $this->directory          = $this->getAddonDirectory('eea-' . $this->addon_string->slug());
        if ($verify) {
            $this->verifyDirectory($this->directory);
        }
    }


    /**
     * Responsible for writing the files for the scaffold.
     */
    public function writeFiles()
    {
        $this->createSubdirectories();
        $template_data = $this->template_arguments->toArray();
        $files_written = $this->createFiles(
            array_map(
                function ($template_path) use ($template_data) {
                    return cliUtils\mustache_render($template_path, $template_data);
                },
                $this->template_arguments->templates($this->directory)
            ),
            $this->template_arguments->isForce()
        );
        $this->logFilesWritten($files_written, 'Files created:');
    }


    /**
     * @return \Nerrad\WPCLI\EE\interfaces\TemplateArgumentsInterface
     */
    public function getTemplateArguments()
    {
        return $this->template_arguments;
    }


    /**
     * If the provided TemplateArguments object has any subdirectories required for the template, then this method
     * will take care of ensuring they are present.
     */
    public function createSubdirectories()
    {
        $wp_filesystem = $this->initWpFilesystem();
        $subdirectories = $this->template_arguments->subdirectories($this->directory);
        if ($subdirectories) {
            foreach ($subdirectories as $path => $directory_to_create) {
                //first array element is $path, second is directory to create.
                if (! $wp_filesystem->exists($path)) {
                    WP_CLI::error(
                        sprintf(
                            'The path given (%s) does not exist and thus we cannot create the directory (%s).',
                            $path,
                            $directory_to_create
                        )
                    );
                }
                //don't attempt to create the directory if it already exists
                if (! $wp_filesystem->exists($path . $directory_to_create)) {
                    if (! $wp_filesystem->mkdir($path . $directory_to_create)) {
                        WP_CLI::error(
                            sprintf(
                                'Unable to create directory (%s)',
                                $path . $directory_to_create
                            )
                        );
                    }
                }
            }
        }
    }
}