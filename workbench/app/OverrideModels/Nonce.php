<?php

namespace Workbench\App\OverrideModels;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Swis\Laravel\LtiProvider\Models\Nonce as BaseLtiNonce;

/**
 * @property string $id
 */
class Nonce extends BaseLtiNonce
{
    use HasUuids;
}
