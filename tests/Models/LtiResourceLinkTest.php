<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\Laravel\LtiProvider\Models\SimpleClient;
use Tests\TestCase;
use Workbench\App\Models\SimpleLtiEnvironment;

class LtiResourceLinkTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_insert_client_id_from_context(): void
    {
        // Arrange
        /** @var SimpleClient $client */
        $client = Factory::factoryForModel(SimpleClient::class)->create();

        $environment = SimpleLtiEnvironment::factory()->create();

        $context = $environment->contexts()->create([
            'client_id' => $client->id,
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $resourceLink = $environment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Baz',
        ]);

        // Assert
        $resourceLinks = $client->resourceLinks()->get();
        $this->assertCount(1, $resourceLinks);

        $this->assertEquals($resourceLink->client_id, $client->id);
    }
}
