<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Context;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * \Swis\Laravel\LtiProvider\Models\LtiContext.
 *
 * @property int                                                                                             $id
 * @property string|null                                                                                     $title
 * @property string                                                                                          $external_context_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject                                                 $settings
 * @property \Illuminate\Support\Carbon|null                                                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                                                 $updated_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \Swis\Laravel\LtiProvider\Models\LtiResourceLink> $resourceLinks
 * @property int|null                                                                                        $resource_links_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereExternalContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiContext whereUpdatedAt($value)
 */
class LtiContext extends Model
{
    use HasLtiClient;
    use HasLtiEnvironment;

    protected $fillable = [
        'client_id',
        'lti_environment_type',
        'lti_environment_id',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.lti-resource-link'), 'lti_context_id');
    }
}
