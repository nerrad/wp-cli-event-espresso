<?php

namespace Nerrad\WPCLI\EE\commands;

use Nerrad\WPCLI\EE\interfaces\BaseCommandInterface;
use WP_CLI;
use EE_Maintenance_Mode;
use EEH_Activation;
use EE_Registry;
use EE_System;
use EED_Ticket_Sales_Monitor;

class ResetData implements BaseCommandInterface
{

    /**
     * Used for holding what type of reset this is.
     * @var
     */
    private $type;

    /**
     * This is used to register the main command.
     */
    public function command()
    {
        WP_CLI::add_command('ee reset', array($this, 'executeCommand'));
    }


    /**
     * Resets EE data on the given site.
     *
     * ## OPTIONS
     * [--type=<type>]
     * : What type of reset to do.  Options:
     *  - "full" means remove all data, delete EE tables,  and deactivate EE
     *  - "fresh" means restore to fresh activation state and leave EE active.
     *  - "reservations" means to reset all ticket reservations to 0
     *  - "caps" means to reset capabilities to default state.
     * ---
     * default: reservations
     * options:
     *  - full
     *  - fresh
     *  - reservations
     *  - caps
     * ---
     *
     * ## EXAMPLES
     *
     *  # Delete all EE data and deactivate EE core.
     *  $ wp ee reset --type=full
     *  Success: All EE data deleted and plugin deactivated.
     *
     * @param       $args
     * @param array $assoc_args
     */
    public function executeCommand($args, array $assoc_args = array())
    {
        $this->setType($assoc_args);
        //for every reset command we prompt the user because its dangerous!
        $this->confirmQuestion();

        $method_to_execute = 'do' . ucwords($this->type);
        //k if we've got here then we just execute the type.
        if (method_exists($this, $method_to_execute)) {
           if ($this->{$method_to_execute}()) {
               $this->successMessage();
           }
        }
    }


    /**
     * Sets the type for the command being executed.
     * @param array $incoming_data
     */
    private function setType($incoming_data)
    {
        $this->type = WP_CLI\Utils\get_flag_value($incoming_data, 'type', 'reservations');
    }


    /**
     * Generate a confirmation question based on the incoming reset type.
     * @return string
     */
    private function confirmQuestion()
    {
        $question = '';
        switch ($this->type)
        {
            case 'full':
                $question = 'Are you sure you want to erase all EE data, delete its tables, and deactivate Event Espresso?';
                break;
            case 'fresh':
                $question = 'This action will erase all EE data and restore to fresh activation state.'
                  . 'Are you sure you want to do that?';
                break;
            case 'reservations':
                $question = 'Are you sure you want to reset all ticket and datetime reservation counts to zero?';
                break;
            case 'caps':
                $question = 'Are you sure you want to reset all Event Espresso capabilities to defaults?';
                break;
        }
        WP_CLI::confirm($question);
    }


    /**
     * Generate a success message based on the incoming reset type.
     */
    private function successMessage()
    {
        $message = '';
        switch ($this->type)
        {
            case 'full':
                $message = 'Event Espresso data and tables are deleted and the plugin is deactivated.';
                break;
            case 'fresh':
                $message = 'Event Espresso data has been deleted and the plugin has been restored to fresh activation state';
                break;
            case 'reservations':
                $message = 'All ticket and datetime reservation counts have been reset to 0';
                break;
            case 'caps':
                $message = 'All Event Espresso capabilities have been restored to their default.';
                break;
        }
        WP_CLI::success($message);
    }


    /**
     * Deletes all EE data and tables and deactivates Event Espresso
     */
    private function doFull()
    {
        try {
            EE_Maintenance_Mode::instance()->set_maintenance_level(EE_Maintenance_Mode::level_0_not_in_maintenance);
            EEH_Activation::delete_all_espresso_cpt_data();
            EEH_Activation::delete_all_espresso_tables_and_data();
            EEH_Activation::remove_cron_tasks();
            WP_CLI::run_command(array('plugin', 'deactivate', 'event-espresso-core'));
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
        return true;
    }


    /**
     * Deletes all EE data and sets the database back up to a fresh activation state.
     */
    private function doFresh()
    {
        try {
            EE_Maintenance_Mode::instance()->set_maintenance_level(EE_Maintenance_Mode::level_0_not_in_maintenance);
            EEH_Activation::delete_all_espresso_cpt_data();
            EEH_Activation::delete_all_espresso_tables_and_data(false);
            EE_Registry::instance()->CFG = EE_Registry::instance()->CFG->reset(true);
            EE_System::instance()->initialize_db_if_no_migrations_required(true);
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
        return true;
    }


    /**
     * Resets the counts for ticket and datetime reservations to 0.
     */
    private function doReservations()
    {
        try {
            if (! EED_Ticket_Sales_Monitor::reset_reservation_counts()) {
                WP_CLI::warning('Ticket and datetime reserved counts were correct and did not need resetting.');
                exit;
            }
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
        return true;
    }


    /**
     * Resets the capabilities for Event Espresso to defaults
     */
    private function doCaps()
    {
        try {
            EE_Registry::instance()->CAP->init_caps(true);
        } catch (\Exception $e) {
            WP_CLI::error($e->getMessage());
        }
        return true;
    }


    /**
     * Return the entire document argument that is used as the third argument when registering a command.
     *
     * @return array
     */
    function commandDocumentArgument()
    {
        //not needed for this command
        return array();
    }

    /**
     * A short description for the command.
     *
     * @return string
     */
    function commandShortDescription()
    {
        //not needed for this command
        return array();
    }

    /**
     * Return the synopsis array which is an array of various descriptive properties for the command.
     *
     * @see  wp cli cookbook (link) for example format of the synopsis arguments.
     * @link https://make.wordpress.org/cli/handbook/commands-cookbook/#wp_cliadd_commands-third-args-parameter
     * @param bool $skip_global  This indicates whether the synopsis returned should include any attributes that a
     *                           parent command might already have.
     * @return array
     */
    function commandSynopsis($skip_global = true)
    {
        //not needed for this command
        return array();
    }
}