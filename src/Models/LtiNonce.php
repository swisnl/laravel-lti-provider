<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\PlatformNonce;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiNonce as LtiNonceInterface;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * \Swis\Laravel\LtiProvider\Models\LtiNonce.
 *
 * @property string                                        $id
 * @property string                                        $lti_environment_type
 * @property string                                        $lti_environment_id
 * @property string                                        $client_id
 * @property string                                        $nonce
 * @property \Illuminate\Support\Carbon                    $expires_at
 * @property \Illuminate\Support\Carbon|null               $created_at
 * @property \Illuminate\Support\Carbon|null               $updated_at
 * @property \App\Models\Client                            $client
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent $ltiEnvironment
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereLtiEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereLtiEnvironmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereNonce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiNonce whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LtiNonce extends Model implements LtiNonceInterface
{
    use HasLtiEnvironment;
    use HasUuids;

    protected $fillable = [
        'client_id',
        'nonce',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model, self>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(app(\Swis\Laravel\LtiProvider\Models\Contracts\LtiClient::class));
    }

    public static function deleteExpired(): void
    {
        self::where('expires_at', '<', now())->delete();
    }

    public function fillLtiPlatformNonce(PlatformNonce $nonce): void
    {
        $nonce->expires = $this->expires_at->getTimestamp();
    }

    public function fillFromLtiPlatformNonce(PlatformNonce $nonce): void
    {
        $this->expires_at = Carbon::createFromTimestamp($nonce->expires);
    }
}
