<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider\Models\Contracts;

interface LtiEnvironment
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Illuminate\Database\Eloquent\Model>
     */
    public function accessTokens(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Illuminate\Database\Eloquent\Model>
     */
    public function contexts(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Illuminate\Database\Eloquent\Model>
     */
    public function nonces(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Illuminate\Database\Eloquent\Model>
     */
    public function resourceLinks(): \Illuminate\Database\Eloquent\Relations\MorphMany;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<\Illuminate\Database\Eloquent\Model>
     */
    public function userResults(): \Illuminate\Database\Eloquent\Relations\MorphMany;
}
