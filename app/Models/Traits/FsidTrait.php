<?php

namespace Plugins\MarketManager\Models\Traits;

use Illuminate\Support\Str;

trait FsidTrait
{
    public static function bootFsidTrait(): void
    {
        static::creating(function ($model) {
            $model->{$model->getFsidKey()} = $model->{$model->getFsidKey()} ?? static::generateFsid(8);
        });
    }

    // generate fsid
    public static function generateFsid($digit): string
    {
        $fsid = Str::random($digit);

        $checkFsid = static::fsid($fsid)->first();

        if (! $checkFsid) {
            return $fsid;
        } else {
            $newFsid = Str::random($digit);
            $checkNewFsid = static::fsid($newFsid)->first();
            if (! $checkNewFsid) {
                return $newFsid;
            }
        }

        return static::generateFsid($digit + 1);
    }

    public function scopeFsid($query, string $fsid): mixed
    {
        return $query->where($this->getFsidKey(), $fsid);
    }
}