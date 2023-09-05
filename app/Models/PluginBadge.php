<?php

namespace Plugins\MarketManager\Models;

class PluginBadge extends Model
{
    const TYPE_BADGE = 1;
    const TYPE_NUMBER = 2;
    const TYPE_TEXT = 3;

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_fskey', 'fskey');
    }
}
