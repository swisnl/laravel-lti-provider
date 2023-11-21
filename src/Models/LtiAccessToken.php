<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\AccessToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property string                          $id
 * @property string                          $access_token
 * @property array                           $scopes
 * @property \Illuminate\Support\Carbon      $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereScopes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiAccessToken whereUpdatedAt($value)
 */
class LtiAccessToken extends Model
{
    use HasLtiClient;
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

    public function fillLtiAccessToken(AccessToken $accessToken): void
    {
        $accessToken->scopes = $this->scopes;
        $accessToken->token = $this->access_token;
        $accessToken->expires = $this->expires_at->getTimestamp();
        $accessToken->created = $this->created_at->getTimestamp();
        $accessToken->updated = $this->updated_at->getTimestamp();
    }

    public function fillFromLtiAccessToken(AccessToken $accessToken): void
    {
        $this->scopes = $accessToken->scopes;
        $this->access_token = $accessToken->token;
        $this->expires_at = Carbon::createFromTimestamp($accessToken->expires);
    }
}
