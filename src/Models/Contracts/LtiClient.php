<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\Platform;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int       $nr
 * @property string    $name
 * @property \DateTime $updated_at
 */
interface LtiClient
{
    public function fillLtiPlatform(Platform $platform): void;

    public function fillFromLtiPlatform(Platform $platform): void;

    /**
     * @return HasMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): HasMany;

    /**
     * @return HasMany<\Swis\Laravel\LtiProvider\Models\LtiContext>
     */
    public function contexts(): HasMany;

    /**
     * @return HasMany<\Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function nonces(): HasMany;

    /**
     * @return HasMany<\Swis\Laravel\LtiProvider\Models\LtiAccessToken>
     */
    public function accessTokens(): HasMany;
}
