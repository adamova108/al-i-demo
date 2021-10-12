<?php

declare(strict_types=1);

namespace AL_Inpsyde\Includes;

use AL_Inpsyde\Admin\Settings;

class UserRemoteRequest extends RemoteRequest
{

    public const AL_USER_TRANSIENT_NAME = 'al_userdata';

    private Settings $settings;

    public function __construct(Settings $settings)
    {

        $this->settings = $settings;
    }

    /**
     * @return array|\WP_Error
     */
    public function AlGetUserdata($userId = null, bool $refresh = false)
    {

        $userTransient = is_numeric($userId) ? self::AL_USER_TRANSIENT_NAME . '_' . $userId : self::AL_USER_TRANSIENT_NAME;
        $url = is_numeric($userId) ? 'https://jsonplaceholder.typicode.com/users/' . $userId : 'https://jsonplaceholder.typicode.com/users';
        $expiry = $this->settings->getTransientExpiry() ?? 300; // Set a fallback of 300 just to make sure

        $alUserData = get_transient($userTransient);

        if ($refresh || empty($alUserData)) {
            $alUserData = $this->AlRemoteRequest($url);
            if (is_wp_error($alUserData)) {
                return $alUserData;
            }
            set_transient($userTransient, $alUserData, $expiry);
        }

        return $alUserData;
    }
}
