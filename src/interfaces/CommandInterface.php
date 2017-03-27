<?php

namespace Nerrad\WPCLI\EE\interfaces;

interface CommandInterface
{
    /**
     * This should take care of registering the command with WP_CLI.
     * @return void
     */
    function addCommand();
}