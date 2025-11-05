<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\AccessToken as CelticAccessToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Traits\HasClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property string $id
 * @property string $access_token
 * @property array<array-key, string> $scopes
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessToken whereUpdatedAt($value)
 */
class AccessToken extends Model
{
    use HasClient;
    use HasLtiEnvironment;

    protected $table = 'lti_access_tokens';

    protected $fillable = [
        'client_id',
        'lti_environment_type',
        'lti_environment_id',
        'access_token',
        'scopes',
        'expires_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'scopes' => '[]',
    ];

    public function fillLtiAccessToken(CelticAccessToken $accessToken): void
    {
        $accessToken->scopes = $this->scopes;
        $accessToken->token = $this->access_token;
        $accessToken->expires = $this->expires_at->getTimestamp();
        $accessToken->created = $this->created_at->getTimestamp();
        $accessToken->updated = $this->updated_at->getTimestamp();
    }

    public function fillFromLtiAccessToken(CelticAccessToken $accessToken): void
    {
        $this->scopes = $accessToken->scopes;
        $this->access_token = $accessToken->token;
        $this->expires_at = Carbon::createFromTimestamp($accessToken->expires);
    }
}
