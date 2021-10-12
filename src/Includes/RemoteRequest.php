<?php

declare(strict_types=1);

namespace AL_Inpsyde\Includes;

abstract class RemoteRequest
{
    /**
     * @param string $url
     * @param array $args
     * @return array|\WP_Error
     */
    public function AlRemoteRequest(string $url, array $args = [])
    {

        $headers = [];
        $args = wp_parse_args(
            $args,
            [
                'method' => 'GET',
                'body' => [],
                'timeout' => 10,
                'sslverify' => false,
                'blocking' => true,
                'stream' => false,
                'filename' => null,
                'headers' => $headers,
            ]
        );

        $method = $args['method'];
        unset($args['method']);

        $response = false;

        if ('GET' === $method) {
            $response = wp_remote_get($url, $args);
        } elseif ('POST' === $method) {
            $response = wp_remote_post($url, $args);
        }
        if (is_wp_error($response)) {
            return $response;
        }
        $response = json_decode($response['body'], true);
        if (isset($response['errcode']) && $response['errcode']) {
            return new \WP_Error($response['errcode'], $response['errmsg']);
        }
        return $response;
    }
}
