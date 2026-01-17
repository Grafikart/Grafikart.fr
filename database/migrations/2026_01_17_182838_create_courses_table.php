<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->text('content');
            $table->boolean('online')->default(false);
            $table->foreignId('attachment_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignId('youtube_thumbnail_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignId('deprecated_by_id')->nullable();
            $table->integer('duration')->default(0);
            $table->string('youtube_id')->nullable();
            $table->string('video_path')->nullable();
            $table->string('source')->nullable();
            $table->string('demo')->nullable();
            $table->boolean('premium')->default(false);
            $table->integer('level')->default(0);
            $table->boolean('force_redirect')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
