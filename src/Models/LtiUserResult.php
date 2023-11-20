<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models;

use ceLTIc\LTI\Enum\IdScope;
use ceLTIc\LTI\UserResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swis\Laravel\LtiProvider\Models\Traits\HasLtiEnvironment;

/**
 * \Swis\Laravel\LtiProvider\Models\LtiUserResult.
 *
 * @property int                                              $id
 * @property int                                              $lti_resource_link_id
 * @property string                                           $external_user_id
 * @property string                                           $external_user_result_id
 * @property \Illuminate\Support\Carbon|null                  $created_at
 * @property \Illuminate\Support\Carbon|null                  $updated_at
 * @property \Swis\Laravel\LtiProvider\Models\LtiResourceLink $resourceLink
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereExternalUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereExternalUserResultId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereLtiResourceLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LtiUserResult whereUpdatedAt($value)
 */
class LtiUserResult extends Model
{
    use HasLtiEnvironment;

    protected $fillable = [
        'lti_environment_type',
        'lti_environment_id',
        'lti_resource_link_id',
        'external_user_id',
        'external_user_result_id',
    ];

    public function fillLtiUserResult(UserResult $userResult): void
    {
        $userResult->setRecordId($this->id);

        $userResult->setResourceLinkId($this->lti_resource_link_id);

        $userResult->ltiUserId = $this->external_user_id;
        $userResult->ltiResultSourcedId = $this->external_user_result_id;
    }

    public function fillFromLtiUserResult(UserResult $userResult): void
    {
        $this->lti_resource_link_id = $userResult->getResourceLink()->getRecordId();
        $this->external_user_id = $userResult->getId(IdScope::IdOnly);
        $this->external_user_result_id = $userResult->ltiResultSourcedId;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\LtiResourceLink, self>
     */
    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(config('lti-provider.class-names.lti-resource-link'), 'lti_resource_link_id');
    }
}
