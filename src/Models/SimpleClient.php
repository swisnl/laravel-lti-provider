<?php

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Platform;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasClientCapabilities;

/**
 * @property int                                             $id
 * @property string                                          $name
 * @property string                                          $key
 * @property string|null                                     $secret
 * @property string|null                                     $public_key
 * @property string|null                                     $lti_platform_id
 * @property string|null                                     $lti_client_id
 * @property string|null                                     $lti_deployment_id
 * @property string|null                                     $lti_version
 * @property string                                          $lti_signature_method
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $lti_profile
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $lti_settings
 * @property \Illuminate\Support\Carbon|null                 $created_at
 * @property \Illuminate\Support\Carbon|null                 $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiDeploymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiPlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiSignatureMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereLtiVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereUpdatedAt($value)
 */
class SimpleClient extends Model implements LtiClient
{
    use HasClientCapabilities;

    protected $table = 'clients';

    protected $fillable = [
        'key',
        'name',
        'secret',
        'public_key',
        'logo',
        'home_url',
        'revoked',
        'lti_platform_id',
        'lti_client_id',
        'lti_deployment_id',
        'lti_version',
        'lti_signature_method',
        'lti_profile',
        'lti_settings',
    ];

    /**
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'grant_types' => 'array',
        'lti_profile' => AsArrayObject::class,
        'lti_settings' => AsArrayObject::class,
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'lti_profile' => '{}',
        'lti_settings' => '{}',
    ];

    protected static function booted(): void
    {
        static::creating(function (SimpleClient $client) {
            $client->secret = $client->secret ?: Str::random(40);
            $client->public_key = $client->public_key ?: Str::random(40);
        });
    }

    public function fillLtiPlatform(Platform $platform): void
    {
        $platform->setRecordId($this->id);
        $platform->name = $this->name;
        $platform->setKey($this->key);
        $platform->secret = $this->secret;
        $platform->platformId = $this->lti_platform_id;
        $platform->clientId = $this->lti_client_id;
        $platform->deploymentId = $this->lti_deployment_id;
        $platform->rsaKey = $this->public_key;
        $platform->signatureMethod = $this->lti_signature_method;
        $platform->consumerName = null;
        $platform->consumerVersion = null;
        $platform->consumerGuid = null;
        $platform->profile = $this->lti_profile;
        $platform->toolProxy = null;
        $platform->setSettings($this->lti_settings->toArray());
        $platform->protected = false;
        $platform->enabled = true;
        $platform->enableFrom = null;
        $platform->enableUntil = null;
        $platform->lastAccess = null;

        $platform->created = $this->created_at->getTimestamp();
        $platform->updated = $this->updated_at->getTimestamp();
    }

    public function fillFromLtiPlatform(Platform $platform): void
    {
        $settings = $platform->getSettings();
        $profile = ! empty($platform->profile) ? $platform->profile : [];

        $this->public_key = $platform->rsaKey;
        $this->lti_platform_id = $platform->platformId;
        $this->lti_client_id = $platform->clientId;
        $this->lti_deployment_id = $platform->deploymentId;
        $this->lti_signature_method = $platform->signatureMethod;
        $this->lti_profile = new ArrayObject($profile);
        $this->lti_settings = new ArrayObject($settings);
    }

    public static function getLtiRecordIdColumn(): string
    {
        return 'id';
    }

    public static function getLtiKeyColumn(): string
    {
        return 'key';
    }

    public function getLtiRecordId(): ?int
    {
        return $this->id;
    }

    public function getLtiKey(): string
    {
        return $this->key;
    }

    public static function getForeignKeyFromPlatform(Platform $platform): int|string
    {
        return $platform->getRecordId();
    }
}
