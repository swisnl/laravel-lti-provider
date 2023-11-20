<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string                                                                                        $lti_environment_type
 * @property string                                                                                        $lti_environment_id
 * @property \Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment $ltiEnvironment
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static whereLtiEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|static whereLtiEnvironmentType($value)
 */
trait HasLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment,self>
     */
    public function ltiEnvironment(): MorphTo
    {
        /** @phpstan-ignore-next-line */
        return $this->morphTo('lti_environment');
    }
}
