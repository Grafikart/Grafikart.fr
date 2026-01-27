<?php

use App\Domains\Course\Path;
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
        Schema::create('path_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Path::class)->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->nullableMorphs('content');
            $table->float('x')->default(0);
            $table->float('y')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_nodes');
    }
};
