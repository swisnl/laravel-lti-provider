<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Swis\Laravel\LtiProvider\ModelDataConnector;
use Swis\Laravel\LtiProvider\Models\AccessToken;
use Swis\Laravel\LtiProvider\Models\Context;
use Swis\Laravel\LtiProvider\Models\Nonce;
use Swis\Laravel\LtiProvider\Models\ResourceLink;
use Swis\Laravel\LtiProvider\Models\UserResult;

trait IsLtiEnvironment
{
    /**
     * @return MorphMany<AccessToken, $this>
     */
    public function accessTokens(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.access-token'), 'lti_environment');
    }

    /**
     * @return MorphMany<Context, $this>
     */
    public function contexts(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.context'), 'lti_environment');
    }

    /**
     * @return MorphMany<Nonce, $this>
     */
    public function nonces(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.nonce'), 'lti_environment');
    }

    /**
     * @return MorphMany<ResourceLink, $this>
     */
    public function resourceLinks(): MorphMany
    {
        /* @phpstan-ignore-next-line */
        return $this->morphMany(config('lti-provider.class-names.resource-link'), 'lti_environment');
    }

    /**
     * @return MorphMany<UserResult, $this>
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
