<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

// use App\Utilities\ConfigUtility;
// use App\Utilities\SubscribeUtility;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Plugins\LaravelConfig\Utilities\ConfigUtility;

return new class extends Migration
{
    protected $fresnsWordBody = [
        // 'type' => SubscribeUtility::TYPE_USER_ACTIVITY,
        // 'fskey' => 'market_manager',
        // 'cmdWord' => 'stats',
    ];

    protected $fresnsConfigItems = [
        // [
        //     'item_tag' => 'market_manager',
        //     'item_key' => 'access_key',
        //     'item_type' => 'string',
        //     'item_value' => null,
        // ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'market_server_host',
            'item_type' => 'string',
            'item_value' => 'https://marketplace.plugins-world.cn',
        ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'system_url',
            'item_type' => 'string',
            'item_value' => null,
        ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'settings_path',
            'item_type' => 'string',
            'item_value' => null,
        ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'install_datetime',
            'item_type' => 'string',
            'item_value' => null,
        ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'build_type',
            'item_type' => 'number',
            'item_value' => 1,
        ],
        [
            'item_tag' => 'market_manager',
            'item_key' => 'github_token',
            'item_type' => 'string',
            'item_value' => null,
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->fresnsConfigItems as $index => $configItem) {
            if ($configItem['item_key'] == 'install_datetime') {
                $configItem['item_value'] = date('Y-m-d H:i:s');
            }

            if ($configItem['item_key'] == 'settings_path') {
                $contents = file_get_contents(dirname(__DIR__, 2) . '/plugin.json');
                $data = json_decode($contents, true);

                $configItem['item_value'] = $data['settingsPath'];
            }

            $this->fresnsConfigItems[$index] = $configItem;
        }

        // addSubscribeItem
        // \FresnsCmdWord::plugin()->addSubscribeItem($this->fresnsWordBody);

        // addKeyValues to Config table
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // removeSubscribeItem
        // \FresnsCmdWord::plugin()->removeSubscribeItem($this->fresnsWordBody);

        // removeKeyValues from Config table
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};
