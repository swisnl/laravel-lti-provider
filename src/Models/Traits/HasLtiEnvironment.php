<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait HasLtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model,self>
     */
    public function ltiEnvironment(): MorphTo
    {
        return $this->morphTo('lti_environment');
    }
}
