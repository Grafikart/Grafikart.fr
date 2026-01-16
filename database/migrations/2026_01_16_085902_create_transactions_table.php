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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->integer('duration');
            $table->integer('price');
            $table->integer('tax');
            $table->string('method');
            $table->string('method_id');
            $table->dateTime('refunded_at')->nullable();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('address');
            $table->string('postal_code');
            $table->string('country_code');
            $table->integer('fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
