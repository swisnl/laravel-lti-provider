<?php

namespace Workbench\Database\Factories\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Swis\Laravel\LtiProvider\Models\SimpleClient;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Swis\Laravel\LtiProvider\Models\SimpleClient>
 */
class SimpleClientFactory extends Factory
{
    protected $model = SimpleClient::class;

    /**
     * {@inheritDoc}
     */
    public function definition()
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'key' => Str::slug($name),
            'secret' => Str::random(40),
            'lti_platform_id' => Str::random(8),
        ];
    }
}
