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
    public static function writeEnv(array $data): void
{
    $envPath = base_path('.env');

    $content = '';
    foreach ($data as $key => $value) {
        $value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
        $content .= "{$key}=\"{$value}\"\n";
    }

    if (!file_put_contents($envPath, $content)) {
        throw new \twentyfouronline\Exceptions\FileWriteFailedException("Failed to write to .env file at $envPath");
    }
}

}
