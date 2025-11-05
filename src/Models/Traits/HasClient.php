<?php

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string|int $client_id
 * @property \Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\Client $client
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|static whereClientId($value)
 */
trait HasClient
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\Client, $this>
     */
    public function client(): BelongsTo
    {
        /* @phpstan-ignore-next-line */
        return $this->belongsTo(config('lti-provider.class-names.client'));
    }
}
