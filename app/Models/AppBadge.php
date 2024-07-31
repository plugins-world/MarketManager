<?php

namespace Plugins\MarketManager\Models;

class AppBadge extends Model
{
    const TYPE_BADGE = 1;
    const TYPE_NUMBER = 2;
    const TYPE_TEXT = 3;
    const TYPE_MAP = [
        AppBadge::TYPE_BADGE => '红点',
        AppBadge::TYPE_NUMBER => '数字',
        AppBadge::TYPE_TEXT => '文字',
    ];

    use Traits\IsEnabledTrait;

    public function app()
    {
        return $this->belongsTo(App::class, 'app_fskey', 'fskey');
    }
}
