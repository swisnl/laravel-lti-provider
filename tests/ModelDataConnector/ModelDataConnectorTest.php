<?php

namespace Tests\ModelDataConnector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Swis\Laravel\LtiProvider\ModelDataConnector;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Tests\TestCase;
use Workbench\App\Models\SimpleLtiEnvironment;

class ModelDataConnectorTest extends TestCase
{
    // This trait contains the actual tests, because we want to run the same
    // tests for a different set of models.
    use ModelDataConnectorTests;

    use RefreshDatabase;

    protected LtiEnvironment $ltiEnvironment;

    protected ModelDataConnector $connector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ltiEnvironment = SimpleLtiEnvironment::factory()->create();
        $this->connector = ModelDataConnector::make($this->ltiEnvironment);
    }
}
