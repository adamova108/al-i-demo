<?php

declare(strict_types=1);

namespace AL_Inpsyde\Frontend;

use AL_Inpsyde\Admin\Settings;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The frontend functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the frontend stylesheet and JavaScript.
 *
 * @since      1.0.0
 *
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Frontend
 */
class Frontend
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
     * @param   $pluginSlug     //The name of the plugin.
     * @param   $version        //The version of this plugin.
     * @param   $settings       //The Settings object.
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
     * @param   $isAdmin    //Whether the current request is for an administrative interface page.
     */
    public function initializeHooks(bool $isAdmin): void
    {
        // Frontend
        if (!$isAdmin) {
            add_action('wp_enqueue_scripts', [$this, 'enqueueStyles'], 10);
            add_action('wp_enqueue_scripts', [$this, 'enqueueScripts'], 10);

            add_action('init', static function () {
                add_rewrite_endpoint('al_users', EP_PERMALINK);
                add_rewrite_endpoint('al_users_page', EP_PERMALINK);
            });

            add_filter('template_include', [$this, 'AlTemplateLocator']);

            add_action('wp_enqueue_scripts', [$this, 'AlMaybeEnqueueJquery']);
        }
    }

    public function AlTemplateLocator(string $template): string
    {
        global $wp_query;

        $pageName = isset($wp_query->query_vars['pagename']) && !empty($wp_query->query_vars['pagename'])
                    ? $wp_query->query_vars['pagename']
                    : (isset($wp_query->query_vars['name']) && !empty($wp_query->query_vars['name'])
                        ? $wp_query->query_vars['name'] : null);

        if (!is_null($pageName)) {
            // At least don't show the 404 in the title
            remove_action('wp_head', '_wp_render_title_tag', 1);

            switch ($pageName) {
                case 'al_users':
                    return plugin_dir_path(__FILE__) . 'partials/al-inpsyde-frontend-template.php';

                case 'al_users_page':
                    return plugin_dir_path(__FILE__) . 'partials/al-inpsyde-frontend-page-template.php';
            }
        }

        return $template;
    }

    public function AlMaybeEnqueueJquery(): void
    {
        wp_enqueue_script('jquery'); // WordPress won't enqueue again if already enqueued
    }

    /**
     * Register the stylesheets for the frontend side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueStyles(): void
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * The reason to register the style before enqueue it:
         * - Conditional loading: When initializing the plugin, do not enqueue your styles, but register them.
         *                        You can enque the style on demand.
         * - Shortcodes: In this way you can load your style only where shortcode appears.
         *              If you enqueue it here it will be loaded on every page, even if the shortcode isn’t used.
         *              Plus, the style will be registered only once, even if the shortcode is used multiple times.
         * - Dependency: The style can be used as dependency, so the style will be automatically loaded, if one style is depend on it.
         */
        $styleId = $this->pluginSlug . '-frontend';

        // For debugging you can separate the minified and the full version on the stylesheet
        //$styleFileName = ($this->settings->getDebug() === true) ? 'al-inpsyde-frontend.css' : 'al-inpsyde-frontend.min.css';
        $styleFileName = 'al-inpsyde-frontend.css';
        $styleUrl = plugin_dir_url(__FILE__) . 'css/' . $styleFileName;
        $version = ($this->settings->getDebug() === true) ? $this->version . '_' . uniqid() : $this->version;
        if (wp_register_style($styleId, $styleUrl, [], $version, 'all') === false) {
            exit(esc_html__('Style could not be registered: ', 'al-inpsyde') . $styleUrl);
        }

        /**
         * If you enque the style here, it will be loaded on every page on the frontend.
         * To load only with a shortcode, move the wp_enqueue_style to the callback function of the add_shortcode.
         */
        wp_enqueue_style($styleId);
    }

    /**
     * Register the JavaScript for the frontend side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueScripts(): void
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * The reason to register the script before enqueue it:
         * - Conditional loading: When initializing the plugin, do not enqueue your scripts, but register them.
         *                        You can enque the script on demand.
         * - Shortcodes: In this way you can load your script only where shortcode appears.
         *              If you enqueue it here it will be loaded on every page, even if the shortcode isn’t used.
         *              Plus, the script will be registered only once, even if the shortcode is used multiple times.
         * - Dependency: The script can be used as dependency, so the script will be automatically loaded, if one script is depend on it.
         */
        $scriptId = $this->pluginSlug . '-frontend';
        // For debugging you can separate the minified and the full version on the script file
        //$scripFileName = ($this->settings->getDebug() === true) ? 'al-inpsyde-frontend.js' : 'al-inpsyde-frontend.min.js';
        $scripFileName = 'al-inpsyde-frontend.js';
        $scriptUrl = plugin_dir_url(__FILE__) . 'js/' . $scripFileName;
        $version = ($this->settings->getDebug() === true) ? $this->version . '_' . uniqid() : $this->version;
        if (wp_register_script($scriptId, $scriptUrl, ['jquery'], $version, false) === false) {
            exit(esc_html__('Script could not be registered: ', 'al-inpsyde') . $scriptUrl);
        }

        /**
         * If you enque the script here, it will be loaded on every page on the frontend.
         * To load only with a shortcode, move the wp_enqueue_script to the callback function of the add_shortcode.
         * If you use the wp_localize_script function, you should place it under the wp_enqueue_script.
         */
        wp_enqueue_script($scriptId);

        wp_localize_script($scriptId, 'alAjaxVars', [
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('al-ajax-nonce'),
        ]);

    }
}
