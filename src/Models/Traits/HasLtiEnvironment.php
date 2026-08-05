<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;

/**
 * @property string $lti_environment_type
 * @property string $lti_environment_id
 * @property Model&LtiEnvironment $ltiEnvironment
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|static whereLtiEnvironmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|static whereLtiEnvironmentType($value)
 */
trait HasLtiEnvironment
{
    /**
     * @return MorphTo<Model&LtiEnvironment, $this>
     */
    public function ltiEnvironment(): MorphTo
    {
        /** @phpstan-ignore-next-line */
        return $this->morphTo();
    }
}
