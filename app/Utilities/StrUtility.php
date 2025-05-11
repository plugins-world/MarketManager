<?php

namespace Plugins\MarketManager\Utilities;

use Illuminate\Support\Str;

class StrUtility
{
    // email
    public static function maskEmail(?string $email = null): ?string
    {
        if (empty($email)) {
            return null;
        }

        $user = strstr($email, '@', true);
        $domain = strstr($email, '@');

        $len = mb_strlen($user);

        $mask = match (true) {
            default => str_repeat('*', 3),
            $len > 3 => str_repeat('*', bcsub($len, 3)),
        };

        $offset = match (true) {
            default => 1,
            $len > 3 => 3,
        };

        $maskUser = substr_replace($user, $mask, $offset);

        return "{$maskUser}{$domain}";
    }

    // number
    public static function maskNumber(?int $number = null): ?string
    {
        if (empty($number)) {
            return null;
        }

        $len = mb_strlen($number);
        if ($len <= 4) {
            return $number;
        }

        $head = substr($number, 0, 2);
        $tail = substr($number, -2);
        $starCount = strlen($number) - 4;
        $star = str_repeat('*', $starCount);

        return $head.$star.$tail;
    }

    // name
    public static function maskName(?string $name = null): ?string
    {
        if (empty($name)) {
            return null;
        }

        $len = mb_strlen($name);
        if ($len < 1) {
            return $name;
        }

        $last = mb_substr($name, -1, 1);
        $lastName = str_repeat('*', $len - 1);

        return $lastName.$last;
    }

    // qualify table name
    public static function qualifyTableName(mixed $model): string
    {
        $modelName = $model;

        if (class_exists($model)) {
            $model = new $model;

            if (! ($model instanceof Model)) {
                throw new \LogicException("unknown table name of $model");
            }

            $modelName = $model->getTable();
        }

        return str_replace(config('database.connections.mysql.prefix'), '', $modelName);
    }

    // qualify url
    public static function qualifyUrl(?string $uri = null, ?string $domain = null): ?string
    {
        if (empty($uri)) {
            return null;
        }

        if (str_contains($uri, '://')) {
            return $uri;
        }

        if (! $domain) {
            return sprintf('%s/%s', config('app.url'), ltrim($uri, '/'));
        }

        return sprintf('%s/%s', rtrim($domain, '/'), ltrim($uri, '/'));
    }

    // Whether it is a pure number
    public static function isPureInt(mixed $variable): bool
    {
        return preg_match('/^\d+?$/', $variable);
    }

    public static function slug(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        if (preg_match("/^[A-Za-z\s]+$/", $text)) {
            $slug = Str::slug($text, '-');
        } else {
            $slug = rawurlencode($text);
        }

        $slug = Str::lower($slug);

        return $slug;
    }
}