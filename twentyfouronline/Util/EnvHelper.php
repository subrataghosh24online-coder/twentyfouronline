<?php

namespace twentyfouronline\Util;

class EnvHelper
{
    /**
     * Parse an environment variable as an array.
     * 
     * @param string $key Environment variable name
     * @param mixed $default Default value if env not set
     * @param array $emptyValues Values to treat as empty and ignore
     * @return array
     */
    public static function parseArray(string $key, $default = null, array $emptyValues = []): array
    {
        $value = env($key, $default);

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $items = array_filter(array_map('trim', explode(',', $value)), function ($item) use ($emptyValues) {
                return !in_array($item, $emptyValues, true);
            });
            return $items;
        }

        if (empty($value) || in_array($value, $emptyValues, true)) {
            return [];
        }

        return (array) $value;
    }
}
