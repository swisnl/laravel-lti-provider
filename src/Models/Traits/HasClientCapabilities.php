<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasClientCapabilities
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\Context, $this>
     */
    public function contexts(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.context'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\ResourceLink, $this>
     */
    public function resourceLinks(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.resource-link'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\Nonce, $this>
     */
    public function nonces(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.nonce'), 'client_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\AccessToken, $this>
     */
    public function accessTokens(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.access-token'), 'client_id');
    }
}
