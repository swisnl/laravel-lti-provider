<?php

namespace Swis\Laravel\LtiProvider\Commands;

use Illuminate\Console\Command;
use Swis\Laravel\LtiProvider\Models\LtiNonce;

class DeleteExpiredLtiNonces extends Command
{
    protected $signature = 'lti:delete-expired-nonces';

    protected $description = 'Cleanup the expired LTI nonces from the database';

    public function handle(): int
    {
        LtiNonce::deleteExpired();

        return static::SUCCESS;
    }
}
