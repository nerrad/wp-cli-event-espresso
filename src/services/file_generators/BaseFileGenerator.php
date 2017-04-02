<?php

namespace Nerrad\WPCLI\EE\services\file_generators;

use Nerrad\WPCLI\EE\entities\AddonString;
use Nerrad\WPCLI\EE\entities\template_arguments\AddonBaseTemplateArguments;
use Nerrad\WPCLI\EE\interfaces\BaseFileGeneratorInterface;
use Nerrad\WPCLI\EE\traits\ScaffoldFiles;
use WP_CLI\Utils as cliUtils;
use Nerrad\WPCLI\EE\services\utils\Locations;

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
     * @param AddonBaseTemplateArguments $template_arguments
     */
    public function __construct(AddonString $addon_string, AddonBaseTemplateArguments $template_arguments)
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
                $this->templates()
            ),
            $this->template_arguments->isForce()
        );
        $this->logFilesWritten($files_written, 'Base Files for addon created.');
    }


    /**
     * Returns a mapped array of generated file names to file source mustache template.
     * @return array
     */
    private function templates()
    {
        $template_path = Locations::templatesPath() . 'base';
        return array(
            $this->directory . 'circle.yml' => $template_path . 'circle.yml.mustache',
            $this->directory . '.gitignore' => $template_path . 'gitignore.mustache',
            $this->directory . 'info.json'  => $template_path . 'info.json.mustache',
            $this->directory . 'LICENSE'    => $template_path . 'LICENSE.mustache',
            $this->directory . $this->template_arguments->getAddonSlug() . '.php'
                                            => $template_path . 'main-file.mustache',
            $this->directory . 'EE_' . $this->template_arguments->getAddonPackage() . '.class.php'
                                            => $template_path . 'main-class.mustache',
            $this->directory . 'README.md'  => $template_path . 'README.md.mustache',
        );
    }

}