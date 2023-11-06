<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\AccessToken;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface LtiAccessToken
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model, \Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function client(): BelongsTo;

    public function fillLtiAccessToken(AccessToken $accessToken): void;

    public function fillFromLtiAccessToken(AccessToken $accessToken): void;
}
