<?php

namespace Workbench\Database\Factories\OverrideModels;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Workbench\App\OverrideModels\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\OverrideModels\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * {@inheritDoc}
     */
    public function definition()
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'secret' => Str::random(40),
            'lti_platform_id' => Str::random(8),
        ];
    }
}
