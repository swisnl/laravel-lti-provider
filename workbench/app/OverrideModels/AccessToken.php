<?php

namespace Workbench\App\OverrideModels;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Swis\Laravel\LtiProvider\Models\AccessToken as BaseLtiAccessToken;

/**
 * @property string $id
 */
class AccessToken extends BaseLtiAccessToken
{
    use HasUuids;
}
