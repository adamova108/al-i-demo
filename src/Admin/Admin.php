<?php

declare(strict_types=1);

namespace AL_Inpsyde\Admin;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Admin
 */
class Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     */
    private string $pluginSlug;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     */
    private string $version;

    /**
     * The settings of this plugin.
     *
     * @since    1.0.0
     */
    private Settings $settings;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param   $pluginSlug     The name of this plugin.
     * @param   $version        The version of this plugin.
     * @param   $settings       The Settings object.
     */
    public function __construct(string $pluginSlug, string $version, Settings $settings)
    {
        $this->pluginSlug = $pluginSlug;
        $this->version = $version;
        $this->settings = $settings;
    }

    /**
     * Register all the hooks of this class.
     *
     * @since    1.0.0
     * @param   $isAdmin    Whether the current request is for an administrative interface page.
     */
    public function initializeHooks(bool $isAdmin): void
    {
        // Admin
        if ($isAdmin) {
            add_action('admin_enqueue_scripts', [$this, 'enqueueStyles'], 10);
            add_action('admin_enqueue_scripts', [$this, 'enqueueScripts'], 10);
        }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since   1.0.0
     * @param   $hook    A screen id to filter the current admin page
     */
    public function enqueueStyles(string $hook): void
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * You can use the $hook parameter to filter for a particular page, for more information see the codex,
         * https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
         *
         * If you are unsure what the $hook name of the current admin page of which you want to conditionally load your script is, add this to your page:
         *  $screen = get_current_screen();
         *  print_r($screen);
         *
         * The reason to register the style before enqueue it:
         * - Conditional loading: When initializing the plugin, do not enqueue your styles, but register them.
         *                        You can enque the style on demand.
         * - Dependency: The style can be used as dependency, so the style will be automatically loaded, if one style is depend on it.
         */
        $styleId = $this->pluginSlug . '-admin';
        //$styleFileName = ($this->settings->getDebug() === true) ? 'al-inpsyde-admin.css' : 'al-inpsyde-admin.min.css';
        $styleFileName = 'al-inpsyde-admin.css';
        $styleUrl = plugin_dir_url(__FILE__) . 'css/' . $styleFileName;
        if (wp_register_style($styleId, $styleUrl, [], $this->version, 'all') === false) {
            exit(esc_html__('Style could not be registered: ', 'al-inpsyde') . $styleUrl);
        }
        /**
         * If you enque the style here, it will be loaded on every Admin page.
         * To load only on a certain page, use the $hook.
         */
        wp_enqueue_style($styleId);
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since   1.0.0
     * @param   $hook    //A screen id to filter the current admin page
     */
    public function enqueueScripts(string $hook): void
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * You can use the $hook parameter to filter for a particular page,
         * for more information see the codex,
         * https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
         *
         * If you are unsure what the $hook name of the current admin page of which you want to conditionally load your script is, add this to your page:
         *  $screen = get_current_screen();
         *  print_r($screen);
         *
         * The reason to register the script before enqueue it:
         * - Conditional loading: When initializing the plugin, do not enqueue your scripts, but register them.
         *                        You can enque the script on demand.
         * - Dependency: The script can be used as dependency, so the script will be automatically loaded, if one script is depend on it.
         */
        $scriptId = $this->pluginSlug . '-admin';
        //$scripFileName = ($this->settings->getDebug() === true) ? 'al-inpsyde-admin.js' : 'al-inpsyde-admin.min.js';
        $scripFileName = 'al-inpsyde-admin.js';
        $scriptUrl = plugin_dir_url(__FILE__) . 'js/' . $scripFileName;
        if (wp_register_script($scriptId, $scriptUrl, ['jquery'], $this->version, false) === false) {
            exit(esc_html__('Script could not be registered: ', 'al-inpsyde') . $scriptUrl);
        }
        /**
         * If you enque the script here, it will be loaded on every Admin page.
         * To load only on a certain page, use the $hook.
         */
        wp_enqueue_script($scriptId);
    }
}
