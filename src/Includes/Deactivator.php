<?php

declare(strict_types=1);

namespace AL_Inpsyde\Includes;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Includes
 */
class Deactivator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate(): void
    {
        // Permission check
        if (!current_user_can('activate_plugins')) {
            // Localization class hasn't been loaded yet.
            wp_die('You don\'t have proper authorization to deactivate a plugin!');
        }

        self::onDeactivation();
    }

    /**
     * The actual tasks performed during deactivation of a plugin.
     * Should handle only stuff that happens during a single site deactivation,
     * as the process will repeated for each site on a Multisite/Network installation
     * if the plugin is deactivated network wide.
     */
    public static function onDeactivation()
    {
    }
}
