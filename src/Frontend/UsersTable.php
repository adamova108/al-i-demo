<?php

declare(strict_types=1);

namespace AL_Inpsyde\Frontend;

use AL_Inpsyde\Includes\UserRemoteRequest;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Contact form and Shortcode template.
 *
 * @link       http://example.com
 * @since      1.0.0
 * @package    AL_Inpsyde
 * @subpackage AL_Inpsyde/Includes
 * @author     Your Name <email@example.com>
 */
class UsersTable
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     */
    private string $pluginSlug;

    /**
     * @var UserRemoteRequest
     */
    private UserRemoteRequest $userRequest;

    /**
     * Initialize the class and set its properties.
     *
     * @since   1.0.0
     * @param   $pluginSlug //The name of the plugin.
     * @param   $version    //The version of this plugin.
     */
    public function __construct(string $pluginSlug, UserRemoteRequest $userRemoteRequest)
    {
        $this->pluginSlug = $pluginSlug;
        $this->userRequest = $userRemoteRequest;
    }

    /**
     * Register all the hooks of this class.
     *
     * @since   1.0.0
     * @param   $isAdmin    //Whether the current request is for an administrative interface page.
     */
    public function initializeHooks(bool $isAdmin): void
    {
        // 'wp_ajax_' hook needs to be run on frontend and admin area too.
        add_action('wp_ajax_al_refresh_users_table', [$this, 'AlRefreshTableAjaxCallback'], 10);
        add_action('wp_ajax_nopriv_al_refresh_users_table', [$this, 'AlRefreshTableAjaxCallback'], 10);

        add_action('wp_ajax_al_get_user_details', [$this, 'AlGetUserDetailsAjaxCallback'], 10);
        add_action('wp_ajax_nopriv_al_get_user_details', [$this, 'AlGetUserDetailsAjaxCallback'], 10);

        add_action('wp_ajax_al_delete_transients', [$this, 'AlDeleteTransientsAjaxCallback'], 10);
        add_action('wp_ajax_nopriv_al_delete_transients', [$this, 'AlDeleteTransientsAjaxCallback'], 10);

        // Frontend
        if (!$isAdmin) {
            add_shortcode('al_users_table', [$this, 'tableShortcode']);
        }
    }

    /**
     * Contact form shortcode.
     *
     * @link https://developer.wordpress.org/reference/functions/add_shortcode/
     * Shortcode attribute names are always converted to lowercase before they are passed into the handler function. Values are untouched.
     *
     * The function called by the shortcode should never produce output of any kind.
     * Shortcode functions should return the text that is to be used to replace the shortcode.
     * Producing the output directly will lead to unexpected results.
     *
     * @since   1.0.0
     * @param   $attributes //Attributes.
     * @param   $content    //The post content.
     * @param   $tag        //The name of the shortcode.
     * @return  //The text that is to be used to replace the shortcode.
     */
    public function tableShortcode($attributes = null, $content = null, string $tag = ''): string
    {
        $attr = shortcode_atts([
            'refresh' => false,
        ], $attributes);

        $output = '';
        $alUserData = $this->userRequest->AlGetUserdata(null, $attr['refresh']);
        $alUserData = apply_filters('al_userdata', $alUserData);

        if (!is_array($alUserData) || empty($alUserData)) {
            return 'Userdata error.';
        }

        $output .= '<section id="al_users_table_container">';

        $output .= '<p style="text-align: center">
                        <a href="javascript:;" onclick="alPlugin.AlRefreshUsersTable();">Refresh Table</a> | 
                        <a href="javascript:;" onclick="alPlugin.AlDeleteTransients();">Delete Transients</a>
                    </p>';

        $output .= '
            <table id="al_users_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($alUserData as $user) {
            $output .= ' 
            <tr>
                <td><a href="javascript:" onclick="alPlugin.AlGetUserDetails(' . $user['id'] . ');">' . $user['id'] . '</a></td>
                <td><a href="javascript:" onclick="alPlugin.AlGetUserDetails(' . $user['id'] . ');">' . $user['name'] . '</a></td>
                <td><a href="javascript:" onclick="alPlugin.AlGetUserDetails(' . $user['id'] . ');">' . $user['username'] . '</a></td>
            </tr>
            <tr class="user_details" id="user_' . $user['id'] . '_details"><td colspan="3"></td></tr>
                ';
        }

        $output .= '
                </tbody>
            </table>
        </section>';

        do_action('al_after_users_table');

        return apply_filters('al_users_table_html', $output);
    }

    public function prettyPrintArray($array, $output = '')
    {

        foreach ($array as $key => $value) {
            //If $value is an array.
            if (is_array($value)) {
                //We need to loop through it.
                $output = $this->prettyPrintArray($value, $output);
            } else {
                //It is not an array, so print it out.
                $output .= '<strong>' . ucfirst($key) . '</strong>: ' . $value . '<br>';
            }
        }

        return $output;
    }

    public function AlGetUserDetailsAjaxCallback(): void
    {
        if (check_ajax_referer('al-ajax-nonce', 'nonce', false) === false) {
            wp_send_json_error('Failed nonce'); // Sends json_encoded success=false.
        }

        if (isset($_REQUEST['user_id'])) {
            $userId = intval($_REQUEST['user_id']);
            $alUser = $this->userRequest->AlGetUserdata($userId);
            if (is_array($alUser)) {
                $html = '<article class="user_details_list">' . $this->prettyPrintArray($alUser) . '</article>';
                wp_send_json_success($html);
            }

            wp_send_json_error('Empty data!');
        }

        wp_send_json_error();
    }

    /**
     *
     */
    public function AlRefreshTableAjaxCallback(): void
    {
        // Verifies the AJAX request
        if (check_ajax_referer('al-ajax-nonce', 'nonce', false) === false) {
            wp_send_json_error('Failed nonce'); // Sends json_encoded success=false.
        }

        wp_send_json_success($this->tableShortcode(['refresh' => true]));
    }

    /**
     *
     */
    public function AlDeleteTransientsAjaxCallback(): void
    {
        if (check_ajax_referer('al-ajax-nonce', 'nonce', false) === false) {
            wp_send_json_error('Failed nonce'); // Sends json_encoded success=false.
        }

        global $wpdb;

        $userTransient = UserRemoteRequest::AL_USER_TRANSIENT_NAME;

        delete_transient($userTransient);

        $maybeGetUserTransients = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name 
                   FROM $wpdb->options 
                   WHERE option_name REGEXP %s",
                $userTransient . '_[0-9]+$'
            )
        );

        if ($maybeGetUserTransients) {
            foreach ($maybeGetUserTransients as $optionName) {
                $optionNameArr = explode('_', $optionName->option_name);
                $userId = end($optionNameArr);
                delete_transient($userTransient . '_' . $userId);
            }
        }

        wp_send_json_success();
    }
}
