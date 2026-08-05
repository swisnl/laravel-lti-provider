<?php

namespace Swis\Laravel\LtiProvider\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Swis\Laravel\LtiProvider\Models\Contracts\Client;

/**
 * @property string|int $client_id
 * @property Model&Client $client
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|static whereClientId($value)
 */
trait HasClient
{
    /**
     * @return BelongsTo<Model&Client, $this>
     */
    public function client(): BelongsTo
    {
        /* @phpstan-ignore-next-line */
        return $this->belongsTo(config('lti-provider.class-names.client'));
    }
}
