<?php

use App\Domains\Course\PathNode;
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
        Schema::create('path_node_links', function (Blueprint $table) {
            $table->foreignIdFor(PathNode::class, 'parent_id')->constrained('path_nodes')->cascadeOnDelete();
            $table->foreignIdFor(PathNode::class, 'child_id')->constrained('path_nodes')->cascadeOnDelete();
            $table->boolean('primary')->default(true);
            $table->primary(['parent_id', 'child_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('path_node_links');
    }
};
