<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Context;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * \Swis\Laravel\LtiProvider\Models\LtiContext.
 *
 * @property int                                                                        $id
 * @property string                                                                     $lti_environment_type
 * @property string                                                                     $lti_environment_id
 * @property string                                                                     $client_id
 * @property string|null                                                                $title
 * @property string                                                                     $external_context_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject                            $settings
 * @property \Illuminate\Support\Carbon|null                                            $created_at
 * @property \Illuminate\Support\Carbon|null                                            $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\Contracts\LtiClient                        $client
 * @property \Illuminate\Database\Eloquent\Model|\Eloquent                              $ltiEnvironment
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\LtiResourceLink> $resourceLinks
 * @property int|null                                                                   $resource_links_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereExternalContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereLtiEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereLtiEnvironmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LtiContext extends Model
{
    use HasLtiEnvironment;

    protected $fillable = [
        'client_id',
        'external_context_id',
        'title',
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

    public function fillLtiContext(Context $context): void
    {
        $context->setRecordId($this->id);

        $context->setPlatformId($this->client->nr);

        $context->title = $this->title;
        $context->ltiContextId = $this->external_context_id;
        $context->setSettings($this->settings->toArray());
    }

    public function fillFromLtiContext(Context $context): void
    {
        $this->client_id = $context->getPlatform()->getKey();

        $this->title = $context->title;
        $this->external_context_id = $context->ltiContextId;
        $this->settings = new ArrayObject($context->getSettings());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model, self>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(app(LtiClient::class));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(LtiResourceLink::class, 'lti_context_id');
    }
}
