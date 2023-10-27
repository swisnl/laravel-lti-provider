<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\ResourceLink;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * \Swis\Laravel\LtiProvider\Models\LtiResourceLink.
 *
 * @property int                                                                      $id
 * @property string                                                                   $lti_environment_type
 * @property string                                                                   $lti_environment_id
 * @property string|null                                                              $client_id
 * @property int|null                                                                 $lti_context_id
 * @property string|null                                                              $title
 * @property string                                                                   $external_resource_link_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject                          $settings
 * @property \Illuminate\Support\Carbon|null                                          $created_at
 * @property \Illuminate\Support\Carbon|null                                          $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\Contracts\LtiClient|null                 $client
 * @property \Swis\Laravel\LtiProvider\Models\LtiContext|null                          $context
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent                            $ltiEnvironment
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\LtiUserResult> $userResults
 * @property int|null                                                                 $user_results_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereExternalResourceLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereLtiContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereLtiEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereLtiEnvironmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiResourceLink whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LtiResourceLink extends Model
{
    use HasLtiEnvironment;

    protected $fillable = [
        'client_id',
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

        $resourceLink->setPlatformId($this->client->nr);
        $resourceLink->setContextId($this->lti_context_id);

        $resourceLink->title = $this->title;
        $resourceLink->ltiResourceLinkId = $this->external_resource_link_id;
        $resourceLink->setSettings($this->settings->toArray());
    }

    public function fillFromLtiResourceLink(ResourceLink $resourceLink): void
    {
        $this->client_id = $resourceLink->getPlatform()->getKey();
        $this->lti_context_id = $resourceLink->getContext()?->getRecordId();

        $this->title = $resourceLink->title;
        $this->external_resource_link_id = $resourceLink->ltiResourceLinkId;
        $this->settings = new ArrayObject($resourceLink->getSettings());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model, self>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(app(LtiClient::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\LtiContext, self>
     */
    public function context(): BelongsTo
    {
        return $this->belongsTo(LtiContext::class, 'lti_context_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): HasMany
    {
        return $this->hasMany(LtiUserResult::class, 'lti_resource_link_id');
    }
}
