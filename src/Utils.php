<?php

namespace NinjaPortal\Portal;

use Exception;
use Lordjoo\LaraApigee\Api\ApigeeX\ApigeeX;
use Lordjoo\LaraApigee\Api\Edge\Edge;
use Lordjoo\LaraApigee\Facades\LaraApigee;

class Utils
{

    /**
     * Get the User model class.
     *
     * @return string|null The User model class or null if not found.
     */
    public static function getUserModel(): ?string
    {
        return self::getModel('User');
    }

    /**
     * Get the Admin model class.
     *
     * @return string|null The Admin model class or null if not found.
     */
    public static function getAdminModel(): ?string
    {
        return self::getModel('Admin');
    }

    /**
     * Get the ApiProduct model class.
     *
     * @return string|null The ApiProduct model class or null if not found.
     */
    public static function getApiProductModel(): ?string
    {
        return self::getModel('ApiProduct');
    }

    /**
     * Get the Audience model class.
     *
     * @return string|null The Audience model class or null if not found.
     */
    public static function getAudienceModel(): ?string
    {
        return self::getModel('Audience');
    }

    /**
     * Get the Category model class.
     *
     * @return string|null The Category model class or null if not found.
     */
    public static function getCategoryModel(): ?string
    {
        return self::getModel('Category');
    }

    /**
     * Get the Menu model class.
     *
     * @return string|null The Menu model class or null if not found.
     */
    public static function getMenuModel(): ?string
    {
        return self::getModel('Menu');
    }

    /**
     * Get the MenuItem model class.
     *
     * @return string|null The MenuItem model class or null if not found.
     */
    public static function getMenuItemModel(): ?string
    {
        return self::getModel('MenuItem');
    }

    /**
     * Get the Setting model class.
     *
     * @return string|null The Setting model class or null if not found.
     */
    public static function getSettingModel(): ?string
    {
        return self::getModel('Setting');
    }

    /**
     * Get the SettingGroup model class.
     *
     * @return string|null The SettingGroup model class or null if not found.
     */
    public static function getSettingGroupModel(): ?string
    {
        return self::getModel('SettingGroup');
    }

    /**
     * Generic method to get any model from the config file.
     *
     * @param string $modelKey The key of the model in the config (e.g., 'User', 'Admin').
     * @return string|null The model class or null if not found.
     */
    protected static function getModel(string $modelKey): ?string
    {
        // Retrieve the 'models' array from the configuration file
        $models = config('ninjaportal.models');

        // Check if the provided model key exists in the config and return the corresponding model class
        return $models[$modelKey] ?? null;
    }

    /**
     * Get the Apigee client based on the platform.
     *
     * @return ApigeeX|Edge
     * @throws Exception
     */
    public static function getApigeeClient(): Edge|ApigeeX
    {
        $platform = self::getPlatform();
        if ($platform === 'edge') {
            return LaraApigee::edge();
        } elseif ($platform === 'apigee') {
            return LaraApigee::apigeex();
        }

        throw new Exception('Invalid platform specified in the configuration.');
    }

    /**
     * Get the platform type (Edge or ApigeeX).
     *
     * @return string|null
     */
    public static function getPlatform(): ?string
    {
        return config('ninjaportal.apigee_platform');
    }


}
