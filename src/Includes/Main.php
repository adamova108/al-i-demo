<?php

declare(strict_types=1);

namespace AL_Inpsyde\Includes;

use AL_Inpsyde\Admin\Admin;
use AL_Inpsyde\Admin\Settings;
use AL_Inpsyde\Frontend\Frontend;
use AL_Inpsyde\Frontend\UsersTable;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Includes
 */
class Main
{
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     */
    protected string $pluginSlug;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     */
    protected string $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->version = AL_PLUGIN_VERSION;
        $this->pluginSlug = AL_PLUGIN_SLUG;
    }

    /**
     * Create the objects and register all the hooks of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineHooks(): void
    {
        $isAdmin = is_admin();

        /**
         * Includes objects - Register all of the hooks related both to the admin area and to the public-facing functionality of the plugin.
         */

        // The Settings' hook initialization runs on Admin area only.
        $settings = new Settings($this->pluginSlug);

        // Users table and shortcode template. Insert [al_users] shortcode to a page to see the result.
        $usersTable = new UsersTable($this->pluginSlug, new UserRemoteRequest($settings));
        $usersTable->initializeHooks($isAdmin);

        /**
         * Admin objects - Register all of the hooks related to the admin area functionality of the plugin.
         */
        if ($isAdmin) {
            $admin = new Admin($this->pluginSlug, $this->version, $settings);
            $admin->initializeHooks($isAdmin);

            $settings->initializeHooks($isAdmin);
        }
        /**
         * Frontend objects - Register all of the hooks related to the public-facing functionality of the plugin.
         */
        else {
            $frontend = new Frontend($this->pluginSlug, $this->version, $settings);
            $frontend->initializeHooks($isAdmin);
        }

        /**/
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run(): void
    {
        $this->defineHooks();
    }
}
