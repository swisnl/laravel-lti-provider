<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\Platform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\AccessToken;
use Swis\Laravel\LtiProvider\Models\Context;
use Swis\Laravel\LtiProvider\Models\Nonce;
use Swis\Laravel\LtiProvider\Models\ResourceLink;

/**
 * @property int $nr
 * @property string $name
 * @property \DateTime $updated_at
 */
interface Client
{
    public static function getLtiRecordIdColumn(): string;

    public static function getLtiKeyColumn(): string;

    public static function getForeignKeyFromPlatform(Platform $platform): int|string;

    public function getLtiRecordId(): ?int;

    public function getLtiKey(): string;

    public function fillLtiPlatform(Platform $platform): void;

    public function fillFromLtiPlatform(Platform $platform): void;

    /**
     * @return HasMany<ResourceLink, $this&Model>
     */
    public function resourceLinks(): HasMany;

    /**
     * @return HasMany<Context, $this&Model>
     */
    public function contexts(): HasMany;

    /**
     * @return HasMany<Nonce, $this&Model>
     */
    public function nonces(): HasMany;

    /**
     * @return HasMany<AccessToken, $this&Model>
     */
    public function accessTokens(): HasMany;
}
