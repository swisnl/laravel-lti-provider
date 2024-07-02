<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\ResourceLink as CelticResourceLink;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Traits\HasClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property int $id
 * @property int|null $lti_context_id
 * @property string|null $title
 * @property string $external_resource_link_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\Context|null $context
 * @property int|null $user_results_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereExternalResourceLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereLtiContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResourceLink whereUpdatedAt($value)
 */
class ResourceLink extends Model
{
    use HasClient;
    use HasLtiEnvironment;

    protected $table = 'lti_resource_links';

    protected $fillable = [
        'client_id',
        'lti_environment_type',
        'lti_environment_id',
        'lti_context_id',
        'title',
        'external_resource_link_id',
        'settings',
    ];

    protected $casts = [
        'settings' => AsArrayObject::class,
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'settings' => '{}',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (ResourceLink $model) {
            if (empty($model->client_id) && ! empty($model->lti_context_id)) {
                $model->client_id = Context::find($model->lti_context_id)?->client_id;
            }
        });
    }

    public function fillLtiResourceLink(CelticResourceLink $resourceLink): void
    {
        $resourceLink->setRecordId($this->id);

        $resourceLink->setPlatformId($this->client->getLtiRecordId());
        $resourceLink->setContextId($this->lti_context_id);

        $resourceLink->title = $this->title;
        $resourceLink->ltiResourceLinkId = $this->external_resource_link_id;
        $resourceLink->setSettings($this->settings->toArray());
    }

    public function fillFromLtiResourceLink(CelticResourceLink $resourceLink): void
    {
        $this->client_id = config('lti-provider.class-names.client')::getForeignKeyFromPlatform($resourceLink->getPlatform());
        $this->lti_context_id = $resourceLink->getContext()?->getRecordId();

        $this->title = $resourceLink->title;
        $this->external_resource_link_id = $resourceLink->ltiResourceLinkId;
        $this->settings = new ArrayObject($resourceLink->getSettings());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\Context, self>
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(config('lti-provider.class-names.context'), 'lti_context_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\UserResult>
     */
    public function userResults(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.user-result'), 'lti_resource_link_id');
    }
}
