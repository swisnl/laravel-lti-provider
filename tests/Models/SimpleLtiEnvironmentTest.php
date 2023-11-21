<?php

namespace Tests\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Workbench\App\Models\SimpleLtiEnvironment;

class SimpleLtiEnvironmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_create_simple_lti_environment(): void
    {
        SimpleLtiEnvironment::factory()->create();

        self::assertDatabaseCount('simple_lti_environments', 1);
    }
}
