<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\ResourceLink;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property int                                              $id
 * @property int|null                                         $lti_context_id
 * @property string|null                                      $title
 * @property string                                           $external_resource_link_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject  $settings
 * @property \Illuminate\Support\Carbon|null                  $created_at
 * @property \Illuminate\Support\Carbon|null                  $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\LtiContext|null $context
 * @property int|null                                         $user_results_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereExternalResourceLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereLtiContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereUpdatedAt($value)
 */
class LtiResourceLink extends Model
{
    use HasLtiClient;
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

        static::saving(function (LtiResourceLink $model) {
            if (empty($model->client_id) && ! empty($model->lti_context_id)) {
                $model->client_id = LtiContext::find($model->lti_context_id)?->client_id;
            }
        });
    }

    public function fillLtiResourceLink(ResourceLink $resourceLink): void
    {
        $resourceLink->setRecordId($this->id);

        $resourceLink->setPlatformId($this->client->getLtiRecordId());
        $resourceLink->setContextId($this->lti_context_id);

        $resourceLink->title = $this->title;
        $resourceLink->ltiResourceLinkId = $this->external_resource_link_id;
        $resourceLink->setSettings($this->settings->toArray());
    }

    public function fillFromLtiResourceLink(ResourceLink $resourceLink): void
    {
        $this->client_id = config('lti-provider.class-names.lti-client')::getForeignKeyFromPlatform($resourceLink->getPlatform());
        $this->lti_context_id = $resourceLink->getContext()?->getRecordId();

        $this->title = $resourceLink->title;
        $this->external_resource_link_id = $resourceLink->ltiResourceLinkId;
        $this->settings = new ArrayObject($resourceLink->getSettings());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\LtiContext, self>
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(config('lti-provider.class-names.lti-context'), 'lti_context_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-user-result'), 'lti_resource_link_id');
    }
}
