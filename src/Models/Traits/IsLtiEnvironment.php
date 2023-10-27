<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Swis\Laravel\LtiProvider\ModelDataConnector;

trait IsLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function accessTokens(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Swis\Laravel\LtiProvider\Models\LtiAccessToken::class, 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiContext>
     */
    public function contexts(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Swis\Laravel\LtiProvider\Models\LtiContext::class, 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function nonces(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Swis\Laravel\LtiProvider\Models\LtiNonce::class, 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Swis\Laravel\LtiProvider\Models\LtiResourceLink::class, 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\Swis\Laravel\LtiProvider\Models\LtiUserResult::class, 'lti_environment');
    }

    public function getDataConnector(): ModelDataConnector
    {
        return new \Swis\Laravel\LtiProvider\ModelDataConnector($this);
    }
}
