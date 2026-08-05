<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swis\Laravel\LtiProvider\Models\AccessToken;
use Swis\Laravel\LtiProvider\Models\Context;
use Swis\Laravel\LtiProvider\Models\Nonce;
use Swis\Laravel\LtiProvider\Models\ResourceLink;
use Swis\Laravel\LtiProvider\Models\UserResult;

interface LtiEnvironment
{
    /**
     * @return MorphMany<AccessToken, $this&Model>
     */
    public function accessTokens(): MorphMany;

    /**
     * @return MorphMany<Context, $this&Model>
     */
    public function contexts(): MorphMany;

    /**
     * @return MorphMany<Nonce, $this&Model>
     */
    public function nonces(): MorphMany;

    /**
     * @return MorphMany<ResourceLink, $this&Model>
     */
    public function resourceLinks(): MorphMany;

    /**
     * @return MorphMany<UserResult, $this&Model>
     */
    public function userResults(): MorphMany;
}
