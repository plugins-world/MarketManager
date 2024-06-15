<?php

namespace Plugins\MarketManager\Utilities;

class DataUtility
{
    public static function getJsonDataFromFile($filepath)
    {
        $filename = basename($filepath);
        $realfilepath = realpath($filepath);
        if (!$realfilepath) {
            throw new \RuntimeException("{$filename} 的 json 数据不存在, 路径为：{$filepath}");
        }

        $content = file_get_contents($realfilepath);
        $data = json_decode($content, true) ?? [];

        return $data;
    }
}
