<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasClientCapabilities
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\Context>
     */
    public function contexts(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.context'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\ResourceLink>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.resource-link'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\Nonce>
     */
    public function nonces(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.nonce'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\AccessToken>
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.access-token'), 'client_id');
    }
}
