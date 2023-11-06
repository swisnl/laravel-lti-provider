<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

use ceLTIc\LTI\ResourceLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface LtiResourceLink
{
    public function fillLtiResourceLink(ResourceLink $resourceLink): void;

    public function fillFromLtiResourceLink(ResourceLink $resourceLink): void;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Model, \Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function client(): BelongsTo;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Swis\Laravel\LtiProvider\Models\LtiContext, \Swis\Laravel\LtiProvider\Models\LtiResourceLink>
     */
    public function context(): BelongsTo;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\Swis\Laravel\LtiProvider\Models\LtiUserResult>
     */
    public function userResults(): HasMany;
}
