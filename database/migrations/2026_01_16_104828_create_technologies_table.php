<?php

use App\Domains\Course\Models\Technology;
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
        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->string('type', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('technology_requirement', function (Blueprint $table) {
            $table->foreignIdFor(Technology::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Technology::class, 'requirement_id')->constrained()->cascadeOnDelete();
            $table->primary(['technology_id', 'requirement_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technology_requirement');
        Schema::dropIfExists('technologies');
    }
};
