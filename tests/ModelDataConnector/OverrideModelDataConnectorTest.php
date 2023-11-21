<?php

namespace Tests\ModelDataConnector;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Swis\Laravel\LtiProvider\ModelDataConnector;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Tests\TestCase;
use Workbench\App\OverrideModels\SimpleLtiEnvironment;

use function Orchestra\Testbench\workbench_path;

class OverrideModelDataConnectorTest extends TestCase
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

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/override_migrations'));
    }

    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config()->set('lti-provider.class-names.lti-client', \Workbench\App\OverrideModels\Client::class);
        config()->set('lti-provider.class-names.lti-access-token', \Workbench\App\OverrideModels\LtiAccessToken::class);
        config()->set('lti-provider.class-names.lti-nonce', \Workbench\App\OverrideModels\LtiNonce::class);
    }
}
