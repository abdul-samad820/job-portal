<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = ['key', 'value', 'group'];

    /**
     * Default values used when a key has never been set yet, so the
     * homepage never shows a blank/broken field before SuperAdmin
     * configures anything.
     */
    public const DEFAULTS = [
        'contact_email' => 'support@jobhub.com',
        'contact_location' => 'New Delhi, India',
        'working_hours' => 'Mon – Sat, 9 AM – 6 PM',
        'linkedin_url' => '',
        'twitter_url' => '',
        'github_url' => '',
    ];

    public const GROUPS = [
        'contact_email' => 'contact',
        'contact_location' => 'contact',
        'working_hours' => 'contact',
        'linkedin_url' => 'social',
        'twitter_url' => 'social',
        'github_url' => 'social',
    ];

    /** Get a single setting value, cached forever until it's updated. */
    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            return static::where('key', $key)->value('value')
                ?? $default
                ?? self::DEFAULTS[$key]
                ?? null;
        });
    }

    /** Set (create or update) a setting and bust its cache immediately. */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => self::GROUPS[$key] ?? 'general']
        );

        Cache::forget("setting.$key");
    }

    /** Get every known setting as a flat [key => value] array, for views. */
    public static function allSettings(): array
    {
        $keys = array_keys(self::DEFAULTS);
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = self::get($key);
        }

        return $result;
    }

    /** Clear all cached settings — call after bulk updates. */
    public static function flushCache(): void
    {
        foreach (array_keys(self::DEFAULTS) as $key) {
            Cache::forget("setting.$key");
        }
    }
}
