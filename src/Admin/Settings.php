<?php

declare(strict_types=1);

namespace AL_Inpsyde\Admin;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Settings of the admin area.
 * Add the appropriate suffix constant for every field ID to take advantake the standardized sanitizer.
 *
 * @since      1.0.0
 *
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Admin
 */
class Settings extends SettingsBase
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     */
    private string $pluginSlug;

    /**
     * The slug name for the menu.
     * Should be unique for this menu page and only include
     * lowercase alphanumeric, dashes, and underscores characters to be compatible with sanitize_key().
     *
     * @since    1.0.0
     */
    private string $menuSlug;

    /**
     * General settings' group name.
     *
     * @since    1.0.0
     */
    private string $generalOptionGroup;

    /**
     * General settings' section.
     * The slug-name of the section of the settings page in which to show the box.
     *
     * @since    1.0.0
     */
    private string $generalSettingsSectionId;

    /**
     * General settings page.
     * The slug-name of the settings page on which to show the section.
     *
     * @since    1.0.0
     */
    private string $generalPage;

    /**
     * Name of general options. Expected to not be SQL-escaped.
     *
     * @since    1.0.0
     */
    private string $generalOptionName;

    /**
     * Collection of options.
     *
     * @since    1.0.0
     */
    private array $generalOptions;

    /**
     * Ids of setting fields.
     */
    private string $debugId;
    private string $transientExpiryId;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    $pluginSlug       //The name of this plugin.
     */
    public function __construct(string $pluginSlug)
    {
        $this->pluginSlug = $pluginSlug;
        $this->menuSlug = $this->pluginSlug;

        /**
         * General
         */
        $this->generalOptionGroup = $pluginSlug . '-general-option-group';
        $this->generalSettingsSectionId = $pluginSlug . '-general-section';
        $this->generalPage = $pluginSlug . '-general';
        $this->generalOptionName = $pluginSlug . '-general';

        $this->debugId = 'debug' . self::CHECKBOX_SUFFIX;
        $this->transientExpiryId = 'expiry' . self::TEXT_SUFFIX;
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
            add_action('admin_menu', [$this, 'setupSettingsMenu'], 10);
            add_action('admin_init', [$this, 'initializeGeneralOptions'], 10);
        }
    }

    /**
     * This function introduces the plugin options into the Main menu.
     */
    public function setupSettingsMenu(): void
    {
        //Add the menu item to the Main menu
        add_menu_page(
            'AL Inpsyde Options', // Page title: The title to be displayed in the browser window for this page.
            'AL Inpsyde',        // Menu title: The text to be used for the menu.
            'manage_options',      // Capability: The capability required for this menu to be displayed to the user.
            $this->menuSlug,               // Menu slug: The slug name to refer to this menu by. Should be unique for this menu page.
            [$this, 'renderSettingsPageContent'],  // Callback: The name of the function to call when rendering this menu's page
            'dashicons-smiley',     // Icon
            81                      // Position: The position in the menu order this item should appear.
        );
    }

    /**
     * Renders the Settings page to display for the Settings menu defined above.
     *
     * @since   1.0.0
     * @param   activeTab       The name of the active tab.
     */
    public function renderSettingsPageContent(string $activeTab = ''): void
    {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // Add error/update messages
        // check if the user have submitted the settings. WordPress will add the "settings-updated" $_GET parameter to the url
        if (isset($_GET['settings-updated'])) {
            // Add settings saved message with the class of "updated"
            add_settings_error($this->pluginSlug, $this->pluginSlug . '-message', __('Settings saved.'), 'success');
        }

        // Show error/update messages
        settings_errors($this->pluginSlug);

        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <h2><?php esc_html_e('AL Inpsyde Options', 'al-inpsyde'); ?></h2>

            <?php $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'general_options'; ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo $this->menuSlug; ?>&tab=general_options" class="nav-tab <?php echo $activeTab === 'general_options' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('General', 'al-inpsyde'); ?></a>
            </h2>

            <form method="post" action="options.php">
                <?php
                if ($activeTab === 'general_options') {
                    settings_fields($this->generalOptionGroup);
                    do_settings_sections($this->generalPage);
                }
                submit_button();
                ?>
            </form>

        </div><!-- /.wrap -->
        <?php
    }

#region GENERAL OPTIONS

    /**
     * Initializes the General Options by registering the Sections, Fields, and Settings.
     *
     * This function is registered with the 'admin_init' hook.
     */
    public function initializeGeneralOptions(): void
    {
        // Get the values of the setting we've registered with register_setting(). It used in the callback functions.
        $this->generalOptions = $this->getGeneralOptions();

        // Add a new section to a settings page.
        add_settings_section(
            $this->generalSettingsSectionId,            // ID used to identify this section and with which to register options
            __('General', 'al-inpsyde'),               // Title to be displayed on the administration page
            [$this, 'generalOptionsCallback'],     // Callback used to render the description of the section
            $this->generalPage                          // Page on which to add this section of options
        );

        // Next, we'll introduce the fields for toggling the visibility of content elements.
        add_settings_field(
            $this->transientExpiryId,                        // ID used to identify the field throughout the theme.
            __('Transient expiry in seconds', 'al-inpsyde'),            // The label to the left of the option interface element.
            [$this, 'transientExpiryCallback'],         // The name of the function responsible for rendering the option interface.
            $this->generalPage,                    // The page on which this option will be displayed.
            $this->generalSettingsSectionId,       // The name of the section to which this field belongs.
            ['label_for' => $this->transientExpiryId]   // Extra arguments used when outputting the field. CSS Class can also be added to the <tr> element with the 'class' key.
        );

        add_settings_field(
            $this->debugId,
            __('Debug', 'al-inpsyde'),
            [$this, 'debugCallback'],
            $this->generalPage,
            $this->generalSettingsSectionId,
            ['label_for' => $this->debugId]
        );

        // Finally, we register the fields with WordPress.
        /**
         * If you want to use the setting in the REST API (wp-json/wp/v2/settings),
         * you???ll need to call register_setting() on the rest_api_init action, in addition to the normal admin_init action.
         */
        $registerSettingArguments = [
            'type' => 'array',
            'description' => '',
            'sanitize_callback' => [$this, 'sanitizeOptionsCallback'],
            'show_in_rest' => false,
        ];
        register_setting($this->generalOptionGroup, $this->generalOptionName, $registerSettingArguments);
    }

    /**
     * Return the General options.
     */
    public function getGeneralOptions(): array
    {
        if (isset($this->generalOptions)) {
            return $this->generalOptions;
        }

        $this->generalOptions = get_option($this->generalOptionName, []);

        // If options don't exist, create them.
        if ($this->generalOptions === []) {
            $this->generalOptions = $this->defaultGeneralOptions();
            update_option($this->generalOptionName, $this->generalOptions);
        }

        return $this->generalOptions;
    }

    /**
     * Provide default values for the General Options.
     *
     * @return array
     */
    private function defaultGeneralOptions(): array
    {
        return [
            $this->debugId => false,
            $this->transientExpiryId => 300,
        ];
    }

    /**
     * This function provides a simple description for the General Options page.
     *
     * It's called from the initializeGeneralOptions function by being passed as a parameter
     * in the add_settings_section function.
     */
    public function generalOptionsCallback(): void
    {
        // Display the settings data for easier examination. Delete it, if you don't need it.
        $this->generalOptions = $this->getGeneralOptions();
        if ($this->generalOptions[$this->debugId]) {
            echo '<p>Display the settings as stored in the database:</p>';
            var_dump($this->generalOptions);
        }

        echo '<p>' . esc_html__('This is the general options for the plugin. Pretty minimalistic...', 'al-inpsyde') . '</p>';
    }

    public function transientExpiryCallback(): void
    {
        printf('<input type="text" id="%s" name="%s[%s]" value="%s" />', $this->transientExpiryId, $this->generalOptionName, $this->transientExpiryId, $this->generalOptions[$this->transientExpiryId]);
    }

    public function debugCallback(): void
    {
        printf('<input type="checkbox" id="%s" name="%s[%s]" value="1" %s />', $this->debugId, $this->generalOptionName, $this->debugId, checked($this->generalOptions[$this->debugId], true, false));
    }

    /**
     * Get Debug option.
     */
    public function getDebug(): bool
    {
        $this->generalOptions = $this->getGeneralOptions();
        return (bool)$this->generalOptions[$this->debugId];
    }

    /**
     * Get Transient Expiry option.
     */
    public function getTransientExpiry(): int
    {
        $this->generalOptions = $this->getGeneralOptions();
        return (int) $this->generalOptions[$this->transientExpiryId];
    }

#endregion
}
