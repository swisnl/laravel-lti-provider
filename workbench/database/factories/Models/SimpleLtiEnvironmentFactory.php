<?php

namespace Workbench\Database\Factories\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\SimpleLtiEnvironment;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\SimpleLtiEnvironment>
 */
class SimpleLtiEnvironmentFactory extends Factory
{
    protected $model = SimpleLtiEnvironment::class;

    /**
     * {@inheritDoc}
     */
    public function definition()
    {
        return [
            'name' => ucfirst($this->faker->words(random_int(3, 5), true)),
        ];
    }
}
