<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Swis\Laravel\LtiProvider\Models\Traits\IsLtiEnvironment;
use Workbench\Database\Factories\Models\SimpleLtiEnvironmentFactory;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment query()
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SimpleLtiEnvironment whereUpdatedAt($value)
 */
class SimpleLtiEnvironment extends Model implements LtiEnvironment
{
    /** @use HasFactory<SimpleLtiEnvironmentFactory> */
    use HasFactory;

    use IsLtiEnvironment;

    protected $fillable = [
        'name',
    ];
}
