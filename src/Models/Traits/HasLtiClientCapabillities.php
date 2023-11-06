<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasLtiClientCapabillities
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function contexts(): HasMany
    {
        return $this->hasMany(config('lti-provider.lti-context'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(config('lti-provider.lti-resource-link'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function nonces(): HasMany
    {
        return $this->hasMany(config('lti-provider.lti-nonce'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function accessTokens(): HasMany
    {
        return $this->hasMany(config('lti-provider.lti-access-token'));
    }
}
