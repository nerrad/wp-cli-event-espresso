<?php

namespace Nerrad\WPCLI\EE\traits;

use cli;
use WP_CLI;
use WP_CLI\Utils as cliTools;

/**
 * ScaffoldFiles
 * This trait is for implementation by Scaffold type components.  It has methods related to working with files as a part
 * of the scaffold.
 *
 * @package Nerrad\WPCLI\EE\traits
 * @subpackage
 * @author  Darren Ethier
 * @since   1.0.0
 */
trait ScaffoldFiles
{
    /**
     * Creates files.
     * @param array $files_and_contents  Keys should be filename and values should be file content.
     * @param bool  $force  Whether to force overwriting the file or not.
     * @return array  Returns an array of files that were successfully written.
     */
    private function createFiles(array $files_and_contents, $force)
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


    /**
     * Executes a prompt if a file is going to be overwritten and gathers response from user.
     * @param string    $filename  The filename that will be overwritten.
     * @param bool      $force     Whether overwriting is forced (will bypass prompt)
     * @return bool     true = overwrite! false = leave alone.
     */
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
     * Use to log to the user what files were written.
     * @param array  $files_written   An array of files written successfully.
     * @param string $prepend_success_messages  A string to prepend the file list log with.
     * @param string $wrapup_message_fail  A string to use if there is an error ($files_written is empty)
     */
    private function logFilesWritten($files_written, $prepend_success_messages = '', $wrapup_message_fail = '')
    {
        if ($files_written) {
            $success_message = '';
            if ($prepend_success_messages) {
                $success_message .= $prepend_success_messages . PHP_EOL;
            }
            $file_table = array();
            foreach ($files_written as $file) {
                $file_table[] = array(
                    'File' => basename($file),
                    'Path' => $file
                );
            }
            WP_CLI::success($success_message);
            cliTools\format_items('table', $file_table, array('File', 'Path'));
        } else {
            if ($wrapup_message_fail) {
                WP_CLI::warning($wrapup_message_fail);
            }
        }
    }


    /**
     * Initializes the WP Filesystem and returns its class for usage.
     * @return \WP_Filesystem_Base
     */
    private function initWpFilesystem()
    {
        global $wp_filesystem;
        WP_Filesystem();
        return $wp_filesystem;
    }


    /**
     * This returns the directory should be for the given add-on slug.  This also verifies that the
     * parent WP plugins directory exists.  This does NOT verify if the addon directory exists.
     *
     * @param string  $slug  The string that will become the root for this add-on.
     * @return string
     */
    private function getAddonDirectory($slug)
    {
        $directory = WP_PLUGIN_DIR . '/' . $slug . '/';
        if (! $this->check_target_directory($directory)) {
            WP_CLI::error("Invalid addon slug specified");
        }
        return $directory;
    }


    /**
     * This gets the Addon directory and verifies it exists.
     * Will exit with a WP_CLI::error if the directory doesn't exist.
     *
     * @param string $directory
     * @return string
     */
    private function verifyDirectory($directory = '')
    {
        if (! is_dir($directory)) {
            WP_CLI::error(sprintf(
                    'Unable to create modules because the addon has not been created yet. (Directory %s does not exist)',
                    $this->directory
                )
            );
        }
    }


    /**
     * Sole purpose is to verify the parent directory of the given target directory
     * equals the WP_PLUGIN_DIR path.
     *
     * @param $target_directory
     * @return bool
     */
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