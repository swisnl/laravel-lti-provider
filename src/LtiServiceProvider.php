<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;

class LtiServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('swis-laravel-lti-provider')
            ->hasMigration('2023_10_26_100000_add_client_and_lti_tables')
            ->publishesServiceProvider('LtiServiceProvider')
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('swisnl/laravel-lti-provider');
            });
    }
}
