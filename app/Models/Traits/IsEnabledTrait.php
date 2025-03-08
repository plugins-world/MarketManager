<?php

namespace Plugins\MarketManager\Models\Traits;

trait IsEnabledTrait
{
    public function scopeIsEnabled($query, bool $isEnabled = true): mixed
    {
        return $query->where('is_enabled', $isEnabled);
    }
}