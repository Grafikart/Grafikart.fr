<?php

use App\Domains\Course\Formation;
use App\Domains\Course\Technology;
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
        Schema::create('formation_technology', function (Blueprint $table) {
            $table->foreignIdFor(Formation::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Technology::class)->constrained()->cascadeOnDelete();
            $table->string('version', 15)->nullable();
            $table->boolean('primary')->default(true);
            $table->primary(['formation_id', 'technology_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_technology');
    }
};
