<?php

namespace Workbench\App\OverrideModels;

use ceLTIc\LTI\Platform;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Swis\Laravel\LtiProvider\Models\Contracts\Client as ClientContract;
use Swis\Laravel\LtiProvider\Models\Traits\HasClientCapabilities;

/**
 * @property string $id
 * @property int $nr
 * @property string $name
 * @property string|null $secret
 * @property string|null $public_key
 * @property string|null $lti_platform_id
 * @property string|null $lti_client_id
 * @property string|null $lti_deployment_id
 * @property string|null $lti_version
 * @property string $lti_signature_method
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $lti_profile
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $lti_settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiDeploymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiPlatformId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiSignatureMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLtiVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereNr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 */
class Client extends Model implements ClientContract
{
    use HasClientCapabilities;
    use HasUuids;

    protected $table = 'lti_clients';

    protected $fillable = [
        'name',
        'secret',
        'public_key',
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
        static::creating(function (Client $client) {
            $client->nr = $client->nr ?: Client::max('nr') + 1;
            $client->secret = $client->secret ?: Str::random(40);
            $client->public_key = $client->public_key ?: Str::random(40);
        });
    }

    public function fillLtiPlatform(Platform $platform): void
    {
        $platform->setRecordId($this->nr);
        $platform->name = $this->name;
        $platform->setKey($this->id);
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
        return 'nr';
    }

    public static function getLtiKeyColumn(): string
    {
        return 'id';
    }

    public function getLtiRecordId(): ?int
    {
        return $this->nr;
    }

    public function getLtiKey(): string
    {
        return $this->id;
    }

    public static function getForeignKeyFromPlatform(Platform $platform): int|string
    {
        return $platform->getKey();
    }
}
