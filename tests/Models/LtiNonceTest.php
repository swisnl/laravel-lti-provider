<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Swis\Laravel\LtiProvider\Models\LtiNonce;
use Swis\Laravel\LtiProvider\Models\SimpleClient;
use Tests\TestCase;
use Workbench\App\Models\SimpleLtiEnvironment;

class LtiNonceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_delete_expired_nonces(): void
    {
        // Arrange
        /** @var SimpleClient $client */
        $client = Factory::factoryForModel(SimpleClient::class)->create();

        $environment = SimpleLtiEnvironment::factory()->create();

        $environment->nonces()->create([
            'client_id' => $client->id,
            'nonce' => 'foo',
            'expires_at' => now()->subMinutes(1),
        ]);

        // Act
        LtiNonce::deleteExpired();

        // Assert
        $this->assertDatabaseMissing('lti_nonces', [
            'nonce' => 'foo',
        ]);
    }
}
