<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Swis\Laravel\LtiProvider\Models\SimpleClient;
use Tests\TestCase;

class SimpleLtiClientTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_create_simple_lti_client(): void
    {
        Factory::factoryForModel(SimpleClient::class)->create();

        self::assertDatabaseCount('lti_clients', 1);
    }
}
