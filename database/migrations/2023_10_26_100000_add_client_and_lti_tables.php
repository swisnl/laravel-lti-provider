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

            // Branding
            $table->text('redirect');
            $table->string('home_url')->nullable();
            $table->string('logo')->nullable();

            // Policies
            $table->boolean('revoked')->default(false);

            // LTI information
            $table->string('lti_platform_id', 255)->nullable();
            $table->string('lti_client_id', 255)->nullable();
            $table->string('lti_deployment_id', 255)->nullable();
            $table->string('lti_version', 10)->nullable();
            $table->string('lti_signature_method', 15)->default('HMAC-SHA1');
            $table->text('lti_profile');
            $table->text('lti_settings');
            $table->string('lti_user_type')->default('external_user');

            $table->timestamps();

            $table->unique(['lti_platform_id', 'lti_client_id', 'lti_deployment_id']);
        });

        Schema::create('lti_nonces', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuidMorphs('lti_environment');

            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('nonce', 50);
            $table->dateTime('expires_at');

            $table->timestamps();

            $table->unique(['client_id', 'nonce']);
        });

        Schema::create('lti_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuidMorphs('lti_environment');

            $table->foreignUuid('client_id')->unique()->constrained('clients')->cascadeOnDelete();

            $table->string('access_token', 2000);
            $table->text('scopes');
            $table->dateTime('expires_at');

            $table->timestamps();
        });

        Schema::create('lti_contexts', function (Blueprint $table) {
            $table->id();

            $table->uuidMorphs('lti_environment');

            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('external_context_id', 255);
            $table->text('settings');

            $table->timestamps();
        });

        Schema::create('lti_resource_links', function (Blueprint $table) {
            $table->id();

            $table->uuidMorphs('lti_environment');

            $table->foreignUuid('client_id')->nullable()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('lti_context_id')->nullable()->constrained('lti_contexts')->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('external_resource_link_id', 255);
            $table->text('settings');

            $table->timestamps();
        });

        Schema::create('lti_user_results', function (Blueprint $table) {
            $table->id();

            $table->uuidMorphs('lti_environment');

            $table->foreignId('lti_resource_link_id')->constrained('lti_resource_links')->cascadeOnDelete();

            $table->string('external_user_id', 255);
            $table->string('external_user_result_id', 255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('lti_user_results');
        Schema::drop('lti_resource_links');
        Schema::drop('lti_contexts');
        Schema::drop('lti_access_tokens');
        Schema::drop('lti_nonces');
        Schema::drop('clients');
    }
};
