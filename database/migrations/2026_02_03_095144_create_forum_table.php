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
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('content');
            $table->boolean('solved');
            $table->integer('messages_count')->default(0);
            $table->timestamps();
        });

        Schema::create('forum_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Domains\Forum\Topic::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete();
            $table->text('content');
            $table->boolean('accepted');
            $table->timestamps();
        });

        Schema::create('forum_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Domains\Forum\TopicTag::class, 'parent_id')->nullable()->constrained('forum_tags')->nullOnDelete();
            $table->string('name');
            $table->string('color');
            $table->boolean('visible')->default(false);
        });

        Schema::create('forum_tag_topic', function (Blueprint $table) {
            $table->foreignIdFor(\App\Domains\Forum\TopicTag::class, 'tag_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Domains\Forum\Topic::class, 'topic_id')->constrained()->cascadeOnDelete();
            $table->primary(['tag_id', 'topic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_tag_topic');
        Schema::dropIfExists('forum_tags');
        Schema::dropIfExists('forum_messages');
        Schema::dropIfExists('forum_topics');
    }
};
