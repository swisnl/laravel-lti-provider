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
     * @return HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function resourceLinks(): HasMany;

    /**
     * @return HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function contexts(): HasMany;

    /**
     * @return HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function nonces(): HasMany;

    /**
     * @return HasMany<\Illuminate\Database\Eloquent\Model>
     */
    public function accessTokens(): HasMany;
}
