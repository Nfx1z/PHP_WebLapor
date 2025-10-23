<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->string('group')->default('general');
            $table->timestamps();
        });

        $defaultSettings = [
            ['key' => 'telegram_bot_token', 'value' => '', 'type' => 'text', 'group' => 'telegram'],
            ['key' => 'telegram_chat_id', 'value' => '', 'type' => 'text', 'group' => 'telegram'],
            ['key' => 'telegram_cooldown', 'value' => '10', 'type' => 'number', 'group' => 'telegram'],
            ['key' => 'dist_thresh', 'value' => '0.20', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'hand_height_above_head', 'value' => '0.06', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'proximity_thresh', 'value' => '0.35', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'iou_overlap_thresh', 'value' => '0.12', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'smooth_frames', 'value' => '3', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'img_size', 'value' => '640', 'type' => 'number', 'group' => 'detection'],
            ['key' => 'flask_api_url', 'value' => 'http://localhost:5000', 'type' => 'text', 'group' => 'general'],
            ['key' => 'save_directory', 'value' => 'storage/app/public/events', 'type' => 'text', 'group' => 'general'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};