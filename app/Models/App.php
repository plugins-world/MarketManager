<?php

namespace Plugins\MarketManager\Models;

class App extends Model
{
    const TYPE_PLUGIN = 1;
    const TYPE_THEME = 2;
    const TYPE_APP_REMOTE = 3;
    const TYPE_APP_DOWNLOAD = 4;

    use Traits\IsEnabledTrait;

    protected $casts = [
        'panel_usages' => 'array',
    ];

    public function getPanelUsagesAttribute($value)
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