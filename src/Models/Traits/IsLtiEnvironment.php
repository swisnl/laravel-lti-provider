<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swis\Laravel\LtiProvider\ModelDataConnector;

trait IsLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\AccessToken, $this>
     */
    public function accessTokens(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.access-token'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Context, $this>
     */
    public function contexts(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.context'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\Nonce, $this>
     */
    public function nonces(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.nonce'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\ResourceLink, $this>
     */
    public function resourceLinks(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.resource-link'), 'lti_environment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Swis\Laravel\LtiProvider\Models\UserResult, $this>
     */
    public function userResults(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.user-result'), 'lti_environment');
    }

    public function getDataConnector(): ModelDataConnector
    {
        return ModelDataConnector::make($this);
    }
}
