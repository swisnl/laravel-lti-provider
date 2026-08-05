<?php

use Swis\Laravel\LtiProvider\Models\AccessToken;
use Swis\Laravel\LtiProvider\Models\Context;
use Swis\Laravel\LtiProvider\Models\Nonce;
use Swis\Laravel\LtiProvider\Models\ResourceLink;
use Swis\Laravel\LtiProvider\Models\SimpleClient;
use Swis\Laravel\LtiProvider\Models\UserResult;

return [
    'class-names' => [
        'client' => SimpleClient::class,
        'context' => Context::class,
        'resource-link' => ResourceLink::class,
        'nonce' => Nonce::class,
        'user-result' => UserResult::class,
        'access-token' => AccessToken::class,
    ],
];
