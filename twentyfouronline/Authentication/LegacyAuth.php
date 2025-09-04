<?php

namespace twentyfouronline\Authentication;

use App\Facades\twentyfouronlineConfig;
use twentyfouronline\Interfaces\Authentication\Authorizer;

class LegacyAuth
{
    protected static $_instance;
    private static $configToClassMap = [
        'mysql' => 'twentyfouronline\Authentication\MysqlAuthorizer',
        'active_directory' => 'twentyfouronline\Authentication\ActiveDirectoryAuthorizer',
        'ldap' => 'twentyfouronline\Authentication\LdapAuthorizer',
        'radius' => 'twentyfouronline\Authentication\RadiusAuthorizer',
        'http-auth' => 'twentyfouronline\Authentication\HttpAuthAuthorizer',
        'ad-authorization' => 'twentyfouronline\Authentication\ADAuthorizationAuthorizer',
        'ldap-authorization' => 'twentyfouronline\Authentication\LdapAuthorizationAuthorizer',
        'sso' => 'twentyfouronline\Authentication\SSOAuthorizer',
    ];

    /**
     * Gets the authorizer based on the config
     *
     * @return Authorizer
     */
    public static function get()
    {
        if (! static::$_instance) {
            $class = self::getClass();
            static::$_instance = new $class;
        }

        return static::$_instance;
    }

    /**
     * The auth mechanism type.
     *
     * @return mixed
     */
    public static function getType()
    {
        return twentyfouronlineConfig::get('auth_mechanism');
    }

    /**
     * Get class for the given or current authentication type/mechanism
     *
     * @param  string  $type
     * @return string
     */
    public static function getClass($type = null)
    {
        if (is_null($type)) {
            $type = self::getType();
        }

        if (! isset(self::$configToClassMap[$type])) {
            throw new \RuntimeException($type . ' not found as auth_mechanism');
        }

        return self::$configToClassMap[$type];
    }

    /**
     * Destroy the existing instance and get a new one - required for tests.
     *
     * @return Authorizer
     */
    public static function reset()
    {
        static::$_instance = null;

        return static::get();
    }
}




