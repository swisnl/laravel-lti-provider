<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasClientCapabilities
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiContext>
     */
    public function contexts(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-context'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-resource-link'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function nonces(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-nonce'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-access-token'));
    }
}
