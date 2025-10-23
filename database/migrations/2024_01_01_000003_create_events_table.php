<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->foreignId('camera_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->integer('persons_count')->default(0);
            $table->text('details')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('telegram_sent')->default(false);
            $table->timestamp('detected_at');
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('detected_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};