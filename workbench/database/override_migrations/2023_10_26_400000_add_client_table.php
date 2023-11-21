<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('nr')->unique();

            // Admin title
            $table->string('name');

            // Keys & secrets
            $table->string('secret', 1024)->nullable();
            $table->text('public_key')->nullable();

            // LTI information
            $table->string('lti_platform_id', 255)->nullable();
            $table->string('lti_client_id', 255)->nullable();
            $table->string('lti_deployment_id', 255)->nullable();
            $table->string('lti_version', 10)->nullable();
            $table->string('lti_signature_method', 15)->default('HMAC-SHA1');
            $table->text('lti_profile');
            $table->text('lti_settings');

            $table->timestamps();

            $table->unique(['lti_platform_id', 'lti_client_id', 'lti_deployment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('clients');
    }
};
