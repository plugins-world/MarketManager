<?php

namespace Plugins\MarketManager\Models;

class TempCallbackContent extends Model
{
    protected $casts = [
        'content' => 'json',
    ];
}