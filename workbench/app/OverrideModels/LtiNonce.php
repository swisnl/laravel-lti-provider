<?php

namespace Workbench\App\OverrideModels;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Swis\Laravel\LtiProvider\Models\LtiNonce as BaseLtiNonce;

/**
 * @property string $id
 */
class LtiNonce extends BaseLtiNonce
{
    use HasUuids;
}
