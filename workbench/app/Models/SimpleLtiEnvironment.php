<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Swis\Laravel\LtiProvider\Models\SimpleClient;
use Swis\Laravel\LtiProvider\Models\Traits\IsLtiEnvironment;

/**
 * @property string                          $id
 * @property string                          $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleClient whereUpdatedAt($value)
 */
class SimpleLtiEnvironment extends Model implements LtiEnvironment
{
    use HasFactory;
    use HasUuids;
    use IsLtiEnvironment;

    protected $fillable = [
        'name',
    ];
}
