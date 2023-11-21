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
        Schema::create('lti_nonces', function (Blueprint $table) {
            $table->id();

            $table->morphs('lti_environment');

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('nonce', 50);
            $table->dateTime('expires_at');

            $table->timestamps();

            $table->unique(['client_id', 'nonce']);
        });

        Schema::create('lti_access_tokens', function (Blueprint $table) {
            $table->id();

            $table->morphs('lti_environment');

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('access_token', 2000);
            $table->text('scopes');
            $table->dateTime('expires_at');

            $table->timestamps();
        });

        Schema::create('lti_contexts', function (Blueprint $table) {
            $table->id();

            $table->morphs('lti_environment');

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('external_context_id', 255);
            $table->text('settings');

            $table->timestamps();
        });

        Schema::create('lti_resource_links', function (Blueprint $table) {
            $table->id();

            $table->morphs('lti_environment');

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->foreignId('lti_context_id')->nullable()->constrained('lti_contexts')->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('external_resource_link_id', 255);
            $table->text('settings');

            $table->timestamps();
        });

        Schema::create('lti_user_results', function (Blueprint $table) {
            $table->id();

            $table->morphs('lti_environment');

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
    }
};
