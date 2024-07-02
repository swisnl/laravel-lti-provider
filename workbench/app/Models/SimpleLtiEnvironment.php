<?php

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Swis\Laravel\LtiProvider\Models\Traits\IsLtiEnvironment;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    use HasFactory;
    use IsLtiEnvironment;

    protected $fillable = [
        'name',
    ];
}
