<?php

use App\Domains\Course\Technology;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technologies', function (Blueprint $table) {
            $table->dropColumn('deprecated');
            $table->foreignIdFor(Technology::class, 'deprecated_by_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('technologies', function (Blueprint $table) {
            $table->dropForeign(['deprecated_by_id']);
            $table->dropColumn('deprecated_by_id');
            $table->boolean('deprecated')->default(false);
        });
    }
};
