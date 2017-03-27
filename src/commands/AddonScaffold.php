<?php

namespace Nerrad\WPCLI\EE\Commands;

use cli;
use WP_CLI;
use WP_CLI\Utils as cliUtils;
use WP_CLI\Process as cliProcess;
use Nerrad\WPCLI\EE\interfaces\CommandInterface;
use Nerrad\WPCLI\EE\services\ComponentBuilder;

class AddonScaffold implements CommandInterface
{

    public function addCommand()
    {
        WP_CLI::add_command('ee scaffold', $this);
    }


    /**
     * Generate starter code for an Event Espresso Addon.
     *
     * The following files are always generated:
     *
     * * `.gitignore` tells which files (or patterns) git should ignore.
     * * `.distignore` tells which files and folders should be ignored in distribution.
     * * `readme.txt` is the readme file for the add-on.
     * * `eea-addon-slug.php` is the main PHP plugin file.
     * * `info.json` is a special file used by the Event Espresso team for packaging releases.
     * * `EE_Addon_Slug.class.php` is the main file used for registering the add-on.
     * * `eea-addon-slug-constants.php` is the file containing all constants used by the add-on.
     * * `README.md` is the readme markdown file.
     * * `circle.yml` is the ci configuration file for circle.
     * * `LICENSE` describes the license for the add-on (defaults to GPLv2)
     *
     * The following files are also included unless the `--skip-tests` is used:
     *
     * * `tests/bootstrap.php` is what is used to bootstrap the add-on for phpunit tests.
     * * `tests/phpunit.xml` is the phpunit config for tests.
     * * `includes/define-constants.php` is the constants used for the tests.
     * * `includes/loader.php` is what actually loads the add-on up for testing.
     * * `testcases/eea-addon-slug-tests.php` is a sample test file with a basic test for the add-on loading.
     *
     *
     * Other files/classes are setup and the configuration array with registering the add-on depends on other flags that
     * are set with this command.
     *
     * The following files/config are included if the `--module=module_slug` flag is used.
     *
     * * `EED_Module_Slug.module.php`  A sample module file ready for use.
     * * The configuration array in `EE_Addon_Slug.class.php` will have the module registered.
     *
     * The following files/config are included if the `--shortcode=shortcode_slug` flag is used.
     *
     * * `EES_Shortcode_Slug.shortcode.php` A sample shortcode file ready for use.  The slug should represent what is the
     * shortcode (i.e. shortcode_slug would result in `[SHORTCODE_SLUG]` for the registered shortcode.
     * * The configuration array in `EE_Addon_Slug.class.php` will have the shortcode registered.
     *
     * The following files/config are included if the `--widget=widget_slug` flag is used.
     *
     * * `EEW_Widget_Slug.widget.php` A sample widget file ready for use.
     * * The configuration array in `EE_Widget_Slug.class.php` will have the widget registered.
     *
     * The following files/config are included if the `--include_config` flag is used.
     *
     * * `EE_Addon_Slug_Config.php`  A sample config file ready for use.
     * * The configuration array in `EE_Addon_Slug.class.php` will have the config registered.
     *
     * The following files/config are included if the `--message_type_name=message_type_slug` option is set. By default,
     * the message type will be registered with the `email` messenger.  However, you can override this by setting the
     * `--message_type_with=html` flag and that will override the default.
     *
     * * `core/messages/EE_Message_Type_Slug_message_type.class.php`  The message type file.
     * * `core/messages/EE_Messages_Email_Message_Type_Slug_Validator.class.php`  The validator class for the message
     * type and any messengers its been registered with.
     * * `core/messages/EED_Message_Type_Slug_Messages.module.php` A module file extending `EED_Messages` that will also
     * be registered as a module with the addon registry array.
     * * `core/messages/templates` an empty directory that can be used for any default templates that will be used.
     * * `core/messages/variations` an empty directory that can be used for any default variations that will be used.
     * * The configuration array in `EE_Addon_Slug.class.php` will have the message type registered (which may need
     * modified depending on the defaults you want set.
     *
     * The following files/config are included if the `--messages_shortcode=shortcode_slug` option is set.  Note, this
     * will not automatically add this shortcode where it is used in any registered message types.  That's logic you'd
     * have to add manually.
     *
     * * `core/messages/shortcodes/EE_Shortcode_Slug_Shortcodes.lib.php`  The shortcode file/class for the new shortcode
     * library.
     * * The related default filters/actions for the shortcode will be set in the main `EE_Addon_Slug` class.  However,
     * these WILL need modified on completion.
     *
     * The following files/config are included if the `--admin_pages=admin_page_slug` option is set.  Note, this can be
     * multiple slugs separated by a comma for multiple admin pages.
     *
     * * `admin/admin_page_slug/Admin_Page_Slug_Admin_Page.core.php`  Main admin page class/file.
     * * `admin/admin_page_slug/Admin_Page_Slug_Admin_Page_Init.core.php` Main admin page init class/file.
     * * `admin/admin_page_slug/espresso_events_Admin_Page_Slug_Hooks.class.php` Example class for linking this admin page
     * with the `espresso_events` route.
     * * `admin/admin_page_slug/templates/admin_page_slug_basic_settings.template.php`  Example template for basic settings.
     * * `admin/admin_page_slug/templates/admin_page_slug_usage_info.template.php` Example template for usage info.
     * * `admin/admin_page_slug/assets/admin_page_slug_admin.css` - Example css file for admin page.
     * * `admin/admin_page_slug/assets/admin_page_slug_admin.js` - Example js file for admin page.
     *
     * ## Options
     *
     * <slug>
     * : The slug used to reference this add-on.  Note, this slug is used for class names, as the plugin slug for the
     * add-on and for other references to the add-on.  Should keep this a unique as possible.
     *
     * [--addon_name=<title>]
     * : What to refer to the add-on as its official title.
     *
     * [--addon_description=<description>]
     * : What is used to describe the add-on.
     *
     * [--addon_author=<author>]
     * : The author for the add-on (used in phpdocs, readme.txt, README.md etc)
     *
     * [--addon_author_uri=<url>]
     * : What to link to for the author.
     *
     * [--plugin_uri=<url>]
     * : What to use when reference to the add-ons reference on the internet is used.
     *
     * [--skip-tests]
     * : Don't generate files for unit testing.
     *
     * [--force]
     * : Overwrite files that already exist.
     *
     * [--message_type=<name>]
     * : This indicates you want a message type(s) to be created with the given slug(s) used for its name.
     *
     * [--message_type_with=<email>]
     * : Indicate what messenger(s) the set message types are to be registered with.
     * ---
     * default: email
     * options:
     *  - html
     *  - email
     *  - pdf
     *  - [any other valid registered messenger slug]
     * ---
     *
     * [--module=<name>]
     * : Requests module(s) setup with the given name(s).
     *
     * [--shortcode=<name>]
     * : Requests shortcode(s) setup with the given shortcode(s).
     *
     * [--widget=<name>]
     * : Requests widget(s) setup with the given name(s).
     *
     * [--messages_shortcode=<slug>]
     * : Requests messages system shortcode(s) setup with the given slug(s).
     *
     * [--admin_pages=<slug>]
     * : Requests admin page(s) setup with the given slug(s).
     *
     * [--namespace=<fully\\qualified\\domain>]
     * : Requests the registration of a namespace. Will attach the namespace to the plugin directory.
     *
     * ## Examples
     *
     *  # Basic add-on skeleton setup with no default components skipping tests.
     *  $ wp ee skeleton addon my-great-ee-addon --skip-tests
     *  Success: Created '.gitignore'
     *  Success: Created '.distignore'
     *  Success: Created 'readme.txt'
     *  Success: Created 'eea-my-great-ee-addon.php'
     *  Success: Created 'info.json'
     *  Success: Created 'EE_My_Great_Ee_Addon.class.php'
     *  Success: Created 'eea-my-great-ee-addon-constants.php'
     *  Success: Created 'README.md'
     *  Success: Created 'circle.yml'
     *  Success: Created 'LICENSE'
     *
     *  # Add-on skeleton with a couple modules and no tests.
     *  $ wp ee skeleton addon my-great-ee-addon --skip-tests --module=module_a,module_b
     *  Success: Created '.gitignore'
     *  Success: Created '.distignore'
     *  Success: Created 'readme.txt'
     *  Success: Created 'eea-my-great-ee-addon.php'
     *  Success: Created 'info.json'
     *  Success: Created 'EE_My_Great_Ee_Addon.class.php'
     *  Success: Created 'eea-my-great-ee-addon-constants.php'
     *  Success: Created 'README.md'
     *  Success: Created 'circle.yml'
     *  Success: Created 'LICENSE'
     *  Success: Created 'EED_Module_A.module.php'
     *  Success: Created 'EED_Module_B.module.php'
     *
     *
     * @param array $args
     * @param array $assoc_args
     * @todo Include payment methods in this.
     */
    public function addon($args, $assoc_args)
    {
        list($addon_slug, $addon_name, $addon_package) = $this->getMainAddonDetails($args[0]);

        $data = wp_parse_args($assoc_args,
            array(
                'addon_slug' => $addon_slug,
                'addon_name' => $addon_name,
                'addon_package' => $addon_package,
                'addon_description' => 'ADDON DESCRIPTION HERE',
                'addon_author' => 'YOUR NAME HERE',
                'addon_author_uri' => 'YOUR SITE HERE',
                'addon_uri' => 'ADDON SITE HERE',
                'addon_tested_up_to' => get_bloginfo('version'),
                'module' => '',
                'shortcode' => '',
                'widget' => '',
                'include_config' => false,
                'message_type_name' => '',
                'message_type_with' => 'email',
                'messages_shortcode' => '',
                'admin_pages' => ''
            )
        );

        $addon_directory = $this->getAddonDirectory($addon_slug);

        $force = cliutils\get_flag_value($assoc_args, 'force');
        $files_written = $this->writeBaseFiles($addon_directory, $data, $force);
        $this->logFilesWritten(
            $files_written,
            'Base Files for addon created:'
        );

        //write main file
        $files_written = $this->writeMainFile($addon_directory, $data, $force);
        $this->logFilesWritten(
            $files_written,
            'Main File for addon created and registration array setup.'
        );

        //module?
        if (cliutils\get_flag_value($assoc_args, 'module')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_module',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['module']
                )
            ));
        }

        //shortcode?
        if (cliutils\get_flag_value($assoc_args, 'shortcode')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_shortcode',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['shortcode']
                )
            ));
        }

        //widget?
        if (cliutils\get_flag_value($assoc_args, 'widget')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_widget',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['widget']
                )
            ));
        }

        //message type?
        if (cliutils\get_flag_value($assoc_args, 'message_type')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_message_type',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['message_type'],
                    'with_messengers' => $data['message_type_with']
                )
            ));
        }

        //messages shortcode?
        if (cliutils\get_flag_value($assoc_args, 'messages_shortcode')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_messages_shortcode',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['messages_shortcode']
                )
            ));
        }

        //admin pages?
        if (cliutils\get_flag_value($assoc_args, 'admin_pages')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_admin_pages',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force,
                    'ignore_main_file_warning' => true,
                    'slugs' => $assoc_args['admin_pages']
                )
            ));
        }

        //tests?
        if (! cliutils\get_flag_value($assoc_args, 'skip-tests')) {
            WP_CLI::run_command(array(
                'ee scaffold addon_tests',
                $addon_slug,
                array(
                    'dir' => $addon_directory,
                    'force' => $force
                )
            ));
        }
    }


    /**
     * Generate starter code for Event Espresso addon modules.
     * This will take care of generating a module file for each slug in the `--slugs` argument. This will **not** modify
     * the registration array in the main addon file.  For that to happen automatically it is recommended to create the
     * module as a part of the `ee scaffold addon --module=<slug>` command.
     * Note: for the first argument on this command, if the directory for the add-on is `eea-addon-a` the slug would be
     * `addon-a`.
     *
     * ## Options
     * <slug>
     * : The slug used to reference the add-on.
     *
     * [--dir=<directory>]
     * : If provided this is where the script will look for the installing the module.
     *
     * [--force]
     * : If set this means any existing files are overwritten.
     *
     * [--ignore_main_file_warning]
     * : If set this prevents the normal warning about main file registration edit.
     *
     * [--slugs=<module_slugs>[,<module_slugs>]]
     * : Modules are setup with the given slug(s).
     *
     * ## Examples
     *
     *  # Single module skeleton setup
     *  $ wp ee skeleton addon_module my-great-addon --slugs=module_a,module_b
     *  Success: Created 'EED_Module_A.module.php'
     *  Success: Created 'EED_Module_B.module.php'
     *  Warning: Modules need to be manually registered in the registration array found in 'EE_My_Great_Addon.class.php'
     *
     * @param array $args
     * @param array $assoc_args
     * @subcommand addon_module
     */
    public function module($args, $assoc_args)
    {
        list($addon_slug, $addon_name, $addon_package) = $this->getMainAddonDetails($args[0]);

        $addon_directory = ! empty($assoc_args['dir']) ? $assoc_args['dir'] : $this->getAddonDirectory($addon_slug);

        //check if addon_directory exists.  If it doesn't then fail.
        if (! is_dir($addon_directory)) {
            WP_CLI::error('Unable to create modules because the addon has not been created yet.');
        }

        $force = cliutils\get_flag_value($assoc_args, 'force');

        //if there are no slugs then error because we need those to create the modules!
        if (! cliutils\get_flag_value($assoc_args, 'slugs')) {
            WP_CLI::error(
                'Unable to create modules because there were no slugs provided.  Use the `--slugs` '
                . 'argument to indicate what you want the module names to be.'
            );
        }
        //@todo finish writing up module subcommand.
    }


    /**
     * @param $args
     * @param $assoc_args
     * @subcommand add_tests
     */
    public function addTests($args, $assoc_args)
    {

    }


    private function getMainAddonDetails($slug)
    {
        $addon_details = array();
        $addon_details['slug'] = strtolower(str_replace('_', '-', $slug));
        $addon_details['name'] = ucwords(str_replace('-', ' ', $addon_details['slug']));
        $addon_details['package'] = str_replace(' ', '_', $addon_details['name']);

        //validate slug.
        if (! $this->validSlug($addon_details['slug'])) {
            WP_CLI::error(
                "Invalid addon slug specified. Slugs can only contain letters, underscores, and hyphens. "
                . "They also must begin with a letter."
            );
        }
        return $addon_details;
    }


    private function getAddonDirectory($slug) {
        $directory = WP_PLUGIN_DIR . "/$slug";
        if (! $this->check_target_directory($directory)) {
            WP_CLI::error("Invalid addon slug specified");
        }
        return $directory;
    }


    private function logFilesWritten($files_written, $prepend_success_messages = '', $wrapup_message_fail = '')
    {
        if ($files_written) {
            if ($prepend_success_messages) {
                WP_CLI::success($prepend_success_messages . PHP_EOL);
            }
            foreach ($files_written as $file) {
                WP_CLI::success(
                    "\t" . $file . PHP_EOL
                );
            }
        } else {
            if ($wrapup_message_fail) {
                WP_CLI::warning($wrapup_message_fail);
            }
        }
    }


    private function writeBaseFiles($directory, $data, $force)
    {
        $files_written = $this->createFiles(array(
            "$directory/eea-{$data['addon_slug']}.php" => cliUtils\mustache_render('addon.mustache', $data),
            "$directory/readme.txt" => cliUtils\mustache_render('addon-readme.txt.mustache', $data),
            "$directory/README.md" => cliUtils\mustache_render('addon-REAME.md.mustache', $data),
            "$directory/.gitignore" => cliUtils\mustache_render('addon-.gitignore.mustache', $data),
            "$directory/.distignore" => cliUtils\mustache_render('addon-.distignore.mustache', $data),
            "$directory/info.json" => cliUtils\mustache_render('addon-info.json.mustache', $data),
            "$directory/eea-{$data['addon_slug']}-constants.php" => cliUtils\mustache_render(
                'addon-constants.mustache',
                $data
            ),
            "$directory/circle.yml" => cliUtils\mustache_render('addon-circle.yml.mustache', $data),
            "$directory/LICENSE" => cliUtils\mustache_render('addon-LICENSE.mustache', $data)
        ), $force);
        return $files_written;
    }


    private function writeMainFile($directory, $data, $force)
    {
        //setup date for the various components that may be present.
        $component_builder = new ComponentBuilder($data);
        $component_builder->addAllComponents();
        $data = array_merge($data, $component_builder->generateAndReturnDataForTemplate());

        $files_written = $this->createFiles(array(
            "$directory/EE_{$data['addon_package']}.class.php" => cliUtils\mustache_render(
                'addon-main-class.mustache',
                $data
            )
        ), $force);
        return $files_written;
    }


    private function createFiles($files_and_contents, $force)
    {
        $wp_filesystem = $this->initWpFilesystem();
        $wrote_files = array();

        foreach ($files_and_contents as $filename => $contents) {
            $should_write_file = $this->promptIfFilesWillBeOverwritten($filename, $force);
            if (! $should_write_file) {
                continue;
            }

            $wp_filesystem->mkdir(dirname($filename));

            if (! $wp_filesystem->put_contents($filename, $contents)) {
                WP_CLI::error("Error with creating file: $filename");
            }
            $wrote_files[] = $filename;
        }
        return $wrote_files;
    }


    private function promptIfFilesWillBeOverwritten($filename, $force)
    {
        $should_write_file = true;
        if (! file_exists($filename)) {
            return true;
        }
        WP_CLI::warning('File already exists.');
        WP_CLI::log($filename);
        if (! $force) {
            do {
                $answer = cli\prompt(
                    'Skip this file, or replace it with scaffolding?',
                    false,
                    '[s/r]'
                );
            } while (! in_array($answer, array('s', 'r', true)));
            $should_write_file = 'r' === $answer;
        }
        $outcome = $should_write_file ? 'Replacing' : 'Skipping';
        WP_CLI::log($outcome . PHP_EOL);
        return $should_write_file;
    }

    /**
     * @return \WP_Filesystem_Base
     */
    private function initWpFilesystem()
    {
        global $wp_filesystem;
        WP_Filesystem();
        return $wp_filesystem;
    }


    private function validSlug($slug_to_validate)
    {
        return ! preg_match('/^[a-z_]\w+$/i', $slug_to_validate);
    }


    private function check_target_directory($target_directory)
    {
        if (realpath($target_directory)) {
            $target_directory = realpath($target_directory);
        }

        $parent_directory = dirname($target_directory);

        if (WP_PLUGIN_DIR === $parent_directory) {
            return true;
        }
        return false;
    }
}