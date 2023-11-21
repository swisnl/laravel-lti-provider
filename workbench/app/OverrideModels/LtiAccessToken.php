<?php

namespace Workbench\App\OverrideModels;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Swis\Laravel\LtiProvider\Models\LtiAccessToken as BaseLtiAccessToken;

/**
 * @property string $id
 */
class LtiAccessToken extends BaseLtiAccessToken
{
    use HasUuids;
}
