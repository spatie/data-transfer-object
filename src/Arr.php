<?php

namespace Spatie\DataTransferObject;

use ArrayAccess;

class Arr
{
    public static function only($array, $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    public static function except($array, $keys): array
    {
        return static::forget($array, $keys);
    }

    public static function forget($array, $keys): array
    {
        $keys = (array) $keys;

        if (count($keys) === 0) {
            return $array;
        }

        foreach ($keys as $key) {
            // If the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            // Check if the key is using dot-notation
            if (! str_contains($key, '.')) {
                continue;
            }

            // If we are dealing with dot-notation, recursively handle i
            $parts = explode('.', $key);
            $key = array_shift($parts);

            if (static::exists($array, $key) && static::accessible($array[$key])) {
                $array[$key] = static::forget($array[$key], implode('.', $parts));

                if (count($array[$key]) === 0) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return $default;
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}
