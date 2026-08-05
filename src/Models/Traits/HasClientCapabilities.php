<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\AccessToken;
use Swis\Laravel\LtiProvider\Models\Context;
use Swis\Laravel\LtiProvider\Models\Nonce;
use Swis\Laravel\LtiProvider\Models\ResourceLink;

trait HasClientCapabilities
{
    /**
     * @return HasMany<Context, $this>
     */
    public function contexts(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.context'), 'client_id');
    }

    /**
     * @return HasMany<ResourceLink, $this>
     */
    public function resourceLinks(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.resource-link'), 'client_id');
    }

    /**
     * @return HasMany<Nonce, $this>
     */
    public function nonces(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.nonce'), 'client_id');
    }

    /**
     * @return HasMany<AccessToken, $this>
     */
    public function accessTokens(): HasMany
    {
        /* @phpstan-ignore-next-line */
        return $this->hasMany(config('lti-provider.class-names.access-token'), 'client_id');
    }
}
