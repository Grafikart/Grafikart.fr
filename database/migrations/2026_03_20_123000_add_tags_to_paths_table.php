<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->string('tags')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('paths', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};
