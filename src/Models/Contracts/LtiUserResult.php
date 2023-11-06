<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\UserResult;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface LtiUserResult
{
    public function fillLtiUserResult(UserResult $userResult): void;

    public function fillFromLtiUserResult(UserResult $userResult): void;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\LtiResourceLink, \Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function resourceLink(): BelongsTo;
}
