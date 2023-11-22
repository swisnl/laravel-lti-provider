<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swis\Laravel\LtiProvider\ModelDataConnector;

trait IsLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function accessTokens(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.lti-access-token'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiContext>
     */
    public function contexts(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.lti-context'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function nonces(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.lti-nonce'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.lti-resource-link'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.lti-user-result'), 'lti_environment');
    }

    public function getDataConnector(): ModelDataConnector
    {
        return ModelDataConnector::make($this);
    }
}
