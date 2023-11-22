<?php

namespace Swis\Laravel\LtiProvider\Commands;

use Illuminate\Console\Command;

class DeleteExpiredLtiNonces extends Command
{
    protected $signature = 'lti:delete-expired-nonces';

    protected $description = 'Cleanup the expired LTI nonces from the database';

    public function handle(): int
    {
        config('lti-provider.class-names.lti-nonce')::deleteExpired();

        return static::SUCCESS;
    }
}
