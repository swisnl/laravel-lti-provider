<?php

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string                                                                                   $client_id
 * @property \Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\LtiClient $client
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static whereClientId($value)
 */
trait HasLtiClient
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\LtiClient, self>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(config('lti-provider.class-names.lti-client'));
    }
}
