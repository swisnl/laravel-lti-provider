<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\PlatformNonce as CelticNonce;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Traits\HasClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property string                          $id
 * @property string                          $nonce
 * @property \Illuminate\Support\Carbon      $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce query()
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce whereNonce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Nonce whereUpdatedAt($value)
 */
class Nonce extends Model
{
    use HasClient;
    use HasLtiEnvironment;

    protected $table = 'lti_nonces';

    protected $fillable = [
        'client_id',
        'lti_environment_type',
        'lti_environment_id',
        'nonce',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function deleteExpired(): void
    {
        self::query()->where('expires_at', '<', now())->delete();
    }

    public function fillLtiPlatformNonce(CelticNonce $nonce): void
    {
        $nonce->expires = $this->expires_at->getTimestamp();
    }

    public function fillFromLtiPlatformNonce(CelticNonce $nonce): void
    {
        $this->expires_at = Carbon::createFromTimestamp($nonce->expires);
    }
}
