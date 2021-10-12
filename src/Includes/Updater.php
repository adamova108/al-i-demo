<?php

declare(strict_types=1);

namespace AL_Inpsyde\Includes;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Update the plugin's database.
 *
 * @since      1.0.0
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Includes
 */
class Updater
{
    /**
     * Update the plugin, by running the incremental updates one by one.
     *
     * For example, if the current DB version is 0, and the target DB version is 2,
     * this function will execute update routines:
     *  - updateRoutine1()
     *  - updateRoutine2()
     *
     * @param   $currentDatabaseVersion     //The currennt database version expected by the plugin.
     * @param   $configurationOptionName    //The ID for the configuration options in the database.
     * @since    1.0.0
     */
    public static function update(int $currentDatabaseVersion, string $configurationOptionName, array $configuration): void
    {

        $installedDatabaseVersion = $configuration['db-version'];
        if ($installedDatabaseVersion < $currentDatabaseVersion) {
            // No PHP timeout for running updates
            set_time_limit(0);

            // Run update routines one by one until the current version number reaches the target version number
            while ($installedDatabaseVersion < $currentDatabaseVersion) {
                $installedDatabaseVersion++;

                // Each db version will require a separate update function for example,
                // for db-version 1, the function name should be updateRoutine1
                $updateRoutineFunctionName = 'updateRoutine' . $installedDatabaseVersion;

                if (is_callable([self, $updateRoutineFunctionName])) {
                    call_user_func([self, $updateRoutineFunctionName]);

                    // Update the configuration option in the database, so that this process can always pick up where it left off
                    $configuration['db-version'] = $installedDatabaseVersion;
                    update_option($configurationOptionName, $configuration);
                } else {
                    wp_die(__('Update routine not callable: ', AL_PLUGIN_SLUG) . __CLASS__ . '\\' . $updateRoutineFunctionName . '()');
                }
            }

            // Set back the PHP timeout to default
            set_time_limit(30);
        }
    }

    /**
     * Update routine for the upcomming database version called by 'update' function
     *
     * @since    1.0.0
     */
    private static function updateRoutine1(): void
    {
        /**
         * Usefull tools to consider:
         *  - array_merge()
         *  - dbDelta()
         *  - wpdb Class
         */
    }
}
