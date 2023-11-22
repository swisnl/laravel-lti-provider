<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

interface LtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function accessTokens(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiContext>
     */
    public function contexts(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function nonces(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): \Illuminate\Database\Eloquent\Relations\MorphMany;
}
