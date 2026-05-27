<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key with optional default
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("setting.{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return static::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Cast the value to the appropriate type
     */
    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'decimal', 'float' => (float) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }

    /**
     * Get service charge percentage (as decimal, e.g., 0.10 for 10%)
     */
    public static function getServiceChargeRate(): float
    {
        return static::get('service_charge_percentage', 10) / 100;
    }

    /**
     * Get service charge percentage for display (e.g., 10 for 10%)
     */
    public static function getServiceChargePercentage(): float
    {
        return static::get('service_charge_percentage', 10);
    }

    public static function getPhoneNumberRegex(): string
    {
        $prefixes = static::getPhoneNumberPrefixes();
        $digits = static::getPhoneNumberDigits();
        $remainingDigits = max(0, $digits - 2);
        $prefixPattern = implode('|', array_map('preg_quote', $prefixes));

        return '/^(' . $prefixPattern . ')\d{' . $remainingDigits . '}$/';
    }

    public static function getPhoneNumberDigits(): int
    {
        return (int) static::get('phone_number_digits', 8);
    }

    public static function getPhoneNumberPrefixes(): array
    {
        $value = (string) static::get('phone_number_prefixes', '16,17');

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    public static function getPhoneNumberPattern(): string
    {
        $prefixes = static::getPhoneNumberPrefixes();
        $prefixPattern = implode('|', array_map('preg_quote', $prefixes));

        return '^(' . $prefixPattern . ')[0-9]{' . max(0, static::getPhoneNumberDigits() - 2) . '}$';
    }

    public static function getPhoneNumberHint(): string
    {
        $prefixes = implode(' or ', static::getPhoneNumberPrefixes());

        return 'Phone number must be ' . static::getPhoneNumberDigits() . ' digits and start with ' . $prefixes . '.';
    }

    public static function getBankAccountDigits(string $bankKey, int $defaultDigits): int
    {
        return (int) static::get($bankKey . '_account_digits', $defaultDigits);
    }
}
