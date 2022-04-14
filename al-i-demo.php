<?php

# -*- coding: utf-8 -*-

/**
 * Plugin Name: AL Inpsyde
 * Plugin URI:
 * Description: Test plugin for Inpsyde
 * Version:     1.0
 * Author: Adam Luzsi
 * License:     GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

declare(strict_types=1);

namespace AL_Inpsyde;

use AL_Inpsyde\Includes\Activator;
use AL_Inpsyde\Includes\Deactivator;
use AL_Inpsyde\Includes\Updater;
use AL_Inpsyde\Includes\Main;

defined('ABSPATH') || die('No direct access!');

/**
 * Autoloader for dependencies
 */
$alAutoload = plugin_dir_path(__FILE__) . 'vendor/autoload.php';

if (is_file($alAutoload)) {
    require_once $alAutoload;
}

define('AL_PLUGIN_VERSION', '1.0.0');

/**
 * The string used to uniquely identify this plugin.
 */
define('AL_PLUGIN_SLUG', 'al-inpsyde');

/**
 * Configuration data
 *  - db-version:   Start with 0 and increment by 1. It should not be updated with every
 *                  plugin update, only when database update is required.
 */

$configuration = [
    'version' => AL_PLUGIN_VERSION,
    'db-version' => 0,
];

/**
 * The ID for the configuration options in the database.
 */
$configurationOptionName = AL_PLUGIN_SLUG . '-configuration';

/**
 * The code that runs during plugin activation.
 * This action is documented in Includes/Activator.php
 */
register_activation_hook(__FILE__, static function () use ($configuration, $configurationOptionName) {
        Activator::activate($configuration, $configurationOptionName);
});

/**
 * The code that runs during plugin deactivation.
 * This action is documented in Includes/Deactivator.php
 */
register_deactivation_hook(
    __FILE__,
    static function () {
        Deactivator::deactivate();
    }
);

/**
 * Update the plugin.
 * It runs every time, when the plugin is started.
 */
add_action(
    'plugins_loaded',
    static function () use ($configuration, $configurationOptionName) {
        Updater::update($configuration['db-version'], $configurationOptionName, $configuration);
    },
    1
);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function runPlugin(): void
{
    $plugin = new Main();
    $plugin->run();
}
runPlugin();
