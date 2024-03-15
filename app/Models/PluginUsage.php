<?php

namespace Plugins\MarketManager\Models;

class PluginUsage extends Model
{
    use Traits\IsEnabledTrait;

    protected $casts = [
        'content' => 'json',
    ];

    public function scopeType($query, int $type)
    {
        return $query->where('usage_type', $type);
    }

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_fskey', 'fskey');
    }
}
