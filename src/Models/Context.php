<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Context as CelticContext;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swis\Laravel\LtiProvider\Models\Traits\HasClient;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property int                                                                                             $id
 * @property string|null                                                                                     $title
 * @property string                                                                                          $external_context_id
 * @property \Illuminate\Database\Eloquent\Casts\ArrayObject                                                 $settings
 * @property \Illuminate\Support\Carbon|null                                                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                                                 $updated_at
 * @property \Illuminate\Database\Eloquent\Collection<int, \Swis\Laravel\LtiProvider\Models\ResourceLink> $resourceLinks
 * @property int|null                                                                                        $resource_links_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Context newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Context newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Context query()
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereExternalContextId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Context whereUpdatedAt($value)
 */
class Context extends Model
{
    use HasClient;
    use HasLtiEnvironment;

    protected $table = 'lti_contexts';

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

    public function fillLtiContext(CelticContext $context): void
    {
        $context->setRecordId($this->id);

        $context->setPlatformId($this->client->getLtiRecordId());

        $context->title = $this->title;
        $context->ltiContextId = $this->external_context_id;
        $context->setSettings($this->settings->toArray());
    }

    public function fillFromLtiContext(CelticContext $context): void
    {
        $this->client_id = config('lti-provider.class-names.client')::getForeignKeyFromPlatform($context->getPlatform());

        $this->title = $context->title;
        $this->external_context_id = $context->ltiContextId;
        $this->settings = new ArrayObject($context->getSettings());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\ResourceLink>
     */
    public function resourceLinks(): HasMany
    {
        return $this->hasMany(config('lti-provider.class-names.resource-link'), 'lti_context_id');
    }
}
