<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Enum\IdScope;
use ceLTIc\LTI\UserResult as CelticUserResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * @property int                                              $id
 * @property int                                              $lti_resource_link_id
 * @property string                                           $external_user_id
 * @property string                                           $external_user_result_id
 * @property \Illuminate\Support\Carbon|null                  $created_at
 * @property \Illuminate\Support\Carbon|null                  $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\ResourceLink $resourceLink
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereExternalUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereExternalUserResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereLtiResourceLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserResult whereUpdatedAt($value)
 */
class UserResult extends Model
{
    use HasLtiEnvironment;

    protected $table = 'lti_user_results';

    protected $fillable = [
        'lti_environment_type',
        'lti_environment_id',
        'lti_resource_link_id',
        'external_user_id',
        'external_user_result_id',
    ];

    public function fillLtiUserResult(CelticUserResult $userResult): void
    {
        $userResult->setRecordId($this->id);

        $userResult->setResourceLinkId($this->lti_resource_link_id);

        $userResult->ltiUserId = $this->external_user_id;
        $userResult->ltiResultSourcedId = $this->external_user_result_id;
    }

    public function fillFromLtiUserResult(CelticUserResult $userResult): void
    {
        $this->lti_resource_link_id = $userResult->getResourceLink()->getRecordId();
        $this->external_user_id = $userResult->getId(IdScope::IdOnly);
        $this->external_user_result_id = $userResult->ltiResultSourcedId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\ResourceLink, self>
     */
    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(config('lti-provider.class-names.resource-link'), 'lti_resource_link_id');
    }
}
