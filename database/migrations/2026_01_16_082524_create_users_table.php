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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Premium
            $table->dateTime('premium_end_at')->nullable();
            $table->string('country', 2)->default('FR');
            $table->datetime('notifications_read_at')->useCurrent();

            // OAuth
            $table->string('discord_id')->nullable();
            $table->string('github_id')->nullable();
            $table->string('google_id')->nullable();
            $table->string('facebook_id')->nullable();

            // Settings
            $table->string('theme')->nullable();
            $table->boolean('html5_player')->default(false);

            // Security
            $table->dateTime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();

            // Payment
            $table->string('stripe_id')->nullable();
            $table->string('invoice_info')->nullable();

            // TOTP
            $table->text('two_factor_secret')->after('password')->nullable();
            $table->text('two_factor_recovery_codes')->after('two_factor_secret')->nullable();
            $table->timestamp('two_factor_confirmed_at')->after('two_factor_recovery_codes')->nullable();

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
