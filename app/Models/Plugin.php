<?php

namespace Plugins\MarketManager\Models;

class Plugin extends Model
{
    const TYPE_PLUGIN = 1;
    const TYPE_PANEL = 2;
    const TYPE_ENGINE = 3;
    const TYPE_THEME = 4;
    const TYPE_STANDALONE = 5;

    use Traits\IsEnabledTrait;

    protected $casts = [
        'scene' => 'array',
    ];

    public function getSceneAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value ?? [];
    }

    public function scopeType($query, $value)
    {
        return $query->where('type', $value);
    }
}