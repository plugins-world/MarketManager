<?php

namespace Plugins\MarketManager\Models;

class PluginCallback extends Model
{
    protected $casts = [
        'content' => 'json',
    ];
}