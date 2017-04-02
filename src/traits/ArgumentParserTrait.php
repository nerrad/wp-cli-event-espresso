<?php

namespace Nerrad\WPCLI\EE\traits;

use WP_CLI;
use Nerrad\WPCLI\EE\entities\AddonString;

/**
 * Contains methods for dealing with receiving and parsing various common argument arrays.
 * This should be implemented by all components and any file needing to get the following objects
 * - Nerrad\WPCLI\EE\entities\AddonString;
 *
 * @package    Nerrad\WPCLI\EE
 * @subpackage utils
 * @author     Darren Ethier
 * @since      1.0.0
 */
trait ArgumentParserTrait
{

    /**
     * Sets up addon details for a given package.
     * @param $slug
     * @return AddonString
     */
    private function getAddonDetails($slug)
    {
        $addon_details = new AddonString($slug);
        //validate slug.
        if (! $this->validSlug($addon_details->slug())) {
            WP_CLI::error(
                "Invalid addon slug specified. Slugs can only contain letters, underscores, and hyphens. "
                . "They also must begin with a letter."
            );
        }
        return $addon_details;
    }


    /**
     * Validates a slug for only allowed characters.
     * @param $slug_to_validate
     * @return bool
     */
    private function validSlug($slug_to_validate)
    {
        return ! preg_match('/^[a-z_]\w+$/i', $slug_to_validate);
    }
}