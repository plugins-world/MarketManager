<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Plugins\LaravelConfig\Utilities\ConfigUtility;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->comment('应用表');

            $table->integer('id', true);
            $table->string('fskey', 64)->unique('plugin_fskey');
            $table->unsignedTinyInteger('type')->default(1);
            $table->string('name', 64);
            $table->string('description');
            $table->string('version', 16);
            $table->string('author', 64);
            $table->string('author_link', 128)->nullable();
            $table->json('panel_usages')->nullable();
            $table->string('app_host', 128)->nullable();
            $table->string('access_path')->nullable();
            $table->string('settings_path', 128)->nullable();
            $table->boolean('is_upgrade')->default(0);
            $table->string('upgrade_code', 32)->nullable();
            $table->string('upgrade_version', 16)->nullable();
            $table->unsignedTinyInteger('is_enabled')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

        Schema::create('app_usages', function (Blueprint $table) {
            $table->comment('插件关联使用表');

            $table->increments('id');
            $table->unsignedTinyInteger('usage_type')->index('app_usage_type');
            $table->string('app_fskey', 64);
            $table->string('name', 128);
            $table->unsignedBigInteger('icon_file_id')->nullable();
            $table->string('icon_file_url')->nullable();
            $table->string('scene', 16)->nullable();
            $table->unsignedTinyInteger('editor_toolbar')->default(0);
            $table->unsignedTinyInteger('editor_number')->nullable();
            $table->unsignedTinyInteger('is_group_admin')->nullable()->default(0);
            $table->unsignedInteger('group_id')->nullable()->index('plugin_usage_group_id');
            $table->string('roles', 128)->nullable();
            $table->string('parameter', 128)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(9);
            $table->unsignedTinyInteger('can_delete')->default(1);
            $table->unsignedTinyInteger('is_enabled')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

        Schema::create('app_badges', function (Blueprint $table) {
            $table->comment('插件徽标数据表');

            $table->bigIncrements('id');
            $table->string('app_fskey', 64);
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('display_type')->default(1)->comment('1.红点 / 2.数字 / 3.文字');
            $table->unsignedSmallInteger('value_number')->nullable();
            $table->string('value_text', 8)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();

            $table->unique(['app_fskey', 'user_id'], 'app_badge_user_id');
        });

        Schema::create('temp_callback_contents', function (Blueprint $table) {
            $table->comment('回调内容表');

            $table->bigIncrements('id');
            $table->string('app_fskey', 64);
            $table->string('key', 64);
            $table->unsignedSmallInteger('type')->default(1);
            switch (config('database.default')) {
                case 'pgsql':
                    $table->jsonb('content')->nullable();
                    break;

                case 'sqlsrv':
                    $table->nvarchar('content', 'max')->nullable();
                    break;

                default:
                    $table->json('content')->nullable();
            }
            $table->unsignedTinyInteger('retention_days')->default(1);
            $table->unsignedTinyInteger('is_enabled')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
        Schema::dropIfExists('app_usages');
        Schema::dropIfExists('app_badges');
        Schema::dropIfExists('temp_callback_contents');
    }
};
