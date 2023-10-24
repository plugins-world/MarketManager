<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run fresns migrations.
     */
    public function up(): void
    {
        $commands = [];

        // 备份
        $commands[] = sprintf('mv %s %s', base_path('public/assets'), base_path('public/assets2_back'));
        $commands[] = sprintf('mv %s %s', base_path('extensions'), base_path('extensions2_back'));
        $commands[] = sprintf('mv %s %s', base_path('storage/extensions'), base_path('storage/extensions2_back'));
        $commands[] = sprintf('cp -r %s %s', base_path('storage/framework/sessions/.gitignore'), base_path('public/assets2_back/.gitignore'));

        // 迁移准备
        $commands[] = sprintf('mkdir -p %s', base_path('public/assets'));
        $commands[] = sprintf('mkdir -p %s', base_path('plugins'));
        $commands[] = sprintf('mkdir -p %s', base_path('storage/plugins/backups'));
        $commands[] = sprintf('mkdir -p %s', base_path('storage/plugins/downloads'));

        // 开始迁移
        $commands[] = sprintf('cp -r %s %s', base_path('public/assets2_back/plugins/*'), base_path('public/assets'));
        $commands[] = sprintf('cp -r %s %s', base_path('extensions2_back/plugins/*'), base_path('plugins'));
        $commands[] = sprintf('cp -r %s %s', base_path('extensions2_back/backups/*'), base_path('storage/plugins/backups'));
        $commands[] = sprintf('cp -r %s %s', base_path('storage/extensions2_back/*'), base_path('storage/plugins/downloads'));

        $commands[] = sprintf('cp -r %s %s', base_path('storage/framework/sessions/.gitignore'), base_path('storage/plugins/.gitignore'));

        $commands[] = sprintf('rm -rf %s', base_path('storage/app/extensions')); // 临时解压目录

        // 执行命令
        foreach ($commands as $command) {
            shell_exec($command);
        }
    }

    /**
     * Reverse fresns migrations.
     */
    public function down(): void
    {
        $commands = [];

        // 备份
        $commands[] = sprintf('mv %s %s', base_path('public/assets'), base_path('public/assets3_back'));
        $commands[] = sprintf('mv %s %s', base_path('plugins'), base_path('plugins3_back'));
        $commands[] = sprintf('mv %s %s', base_path('storage/plugins'), base_path('storage/plugins3_back'));

        // 迁移准备
        $commands[] = sprintf('mkdir -p %s', base_path('public/assets/plugins'));
        $commands[] = sprintf('mkdir -p %s', base_path('public/assets/themes'));
        $commands[] = sprintf('mkdir -p %s', base_path('extensions/backups'));
        $commands[] = sprintf('mkdir -p %s', base_path('extensions/plugins'));
        $commands[] = sprintf('mkdir -p %s', base_path('extensions/themes'));
        $commands[] = sprintf('mkdir -p %s', base_path('storage/extensions'));

        // 开始迁移
        $commands[] = sprintf('cp -r %s %s', base_path('public/assets3_back/*'), base_path('public/assets/plugins'));
        $commands[] = sprintf('cp -r %s %s', base_path('plugins3_back/*'), base_path('extensions/plugins'));
        $commands[] = sprintf('cp -r %s %s', base_path('storage/plugins/downloads/*'), base_path('storage/extensions'));
        $commands[] = sprintf('cp -r %s %s', base_path('storage/plugins/backups/*'), base_path('extensions/backups'));


        $commands[] = sprintf('cp -r %s %s', base_path('storage/framework/sessions/.gitignore'), base_path('storage/extensions/.gitignore'));

        $commands[] = sprintf('rm -rf %s', base_path('storage/plugins3_back/.tmp')); // 临时解压目录

        // 执行命令
        foreach ($commands as $command) {
            shell_exec($command);
        }
    }
};
