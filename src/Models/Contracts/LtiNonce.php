<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\PlatformNonce;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface LtiNonce
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, \Swis\Laravel\LtiProvider\Models\LtiNonce>
     */
    public function client(): BelongsTo;

    public static function deleteExpired(): void;

    public function fillLtiPlatformNonce(PlatformNonce $nonce): void;

    public function fillFromLtiPlatformNonce(PlatformNonce $nonce): void;
}
