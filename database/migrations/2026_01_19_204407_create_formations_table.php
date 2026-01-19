<?php

use App\Domains\Attachment\Attachment;
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
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->boolean('online')->default(false);
            $table->foreignIdFor(Attachment::class)->nullable()->constrained()->nullOnDelete();
            $table->text('short')->nullable();
            $table->json('chapters')->default('[]');
            $table->string('youtube_playlist')->nullable();
            $table->string('links')->nullable();
            $table->integer('level')->default(0);
            $table->foreignId('deprecated_by_id')->nullable()->constrained('formations')->nullOnDelete();
            $table->boolean('force_redirect')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
