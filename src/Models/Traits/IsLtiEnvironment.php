<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swis\Laravel\LtiProvider\ModelDataConnector;

trait IsLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\AccessToken>
     */
    public function accessTokens(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.access-token'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Context>
     */
    public function contexts(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.context'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Nonce>
     */
    public function nonces(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.nonce'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\ResourceLink>
     */
    public function resourceLinks(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.resource-link'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\UserResult>
     */
    public function userResults(): MorphMany
    {
        return $this->morphMany(config('lti-provider.class-names.user-result'), 'lti_environment');
    }

    public function getDataConnector(): ModelDataConnector
    {
        return ModelDataConnector::make($this);
    }
}
