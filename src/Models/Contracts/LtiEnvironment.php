<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

interface LtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\AccessToken>
     */
    public function accessTokens(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Context>
     */
    public function contexts(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Nonce>
     */
    public function nonces(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\ResourceLink>
     */
    public function resourceLinks(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\UserResult>
     */
    public function userResults(): \Illuminate\Database\Eloquent\Relations\MorphMany;
}
