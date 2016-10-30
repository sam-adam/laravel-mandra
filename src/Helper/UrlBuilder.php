<?php

namespace LaravelMandra\Helper;

/**
 * Class UrlBuilder
 *
 * @package LaravelMandra\Helper
 */
class UrlBuilder
{
    /**
     * Build URL for logging
     *
     * @param string $baseUrl
     * @param array  $data
     * @param array  $loggedParams
     *
     * @return string
     */
    public static function buildUrl($baseUrl, array $data, array $loggedParams = [])
    {
        $baseUrlParts   = parse_url($baseUrl);
        $baseUrlQueries = [];

        parse_str(data_get($baseUrlParts, 'query', ''), $baseUrlQueries);

        foreach ($loggedParams as $loggedParam) {
            if (isset($data[$loggedParam])) {
                $param = $data[$loggedParam];

                if (is_object($param)) {
                    continue;
                }

                if (!is_array($param)) {
                    $baseUrlQueries[$param] = $param;
                } else {
                    $param = array_flatten($param);

                    $baseUrlQueries = array_merge($baseUrlQueries, $param);
                }
            }
        }

        $baseUrlParts['query'] = http_build_query($baseUrlQueries);

        return http_build_url($baseUrlParts);
    }
}