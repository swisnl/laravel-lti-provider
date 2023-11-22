<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Swis\Laravel\LtiProvider\LtiServiceProvider;

use function Orchestra\Testbench\package_path;
use function Orchestra\Testbench\workbench_path;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            function (string $modelName): string {
                $modelNameParts = explode('\\', $modelName);
                $modelName = implode('\\', array_slice($modelNameParts, -2, 2));

                return 'Workbench\\Database\\Factories\\'.$modelName.'Factory';
            }
        );
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(workbench_path('database/migrations'));
        $this->loadMigrationsFrom(package_path('database/migrations'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            LtiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
