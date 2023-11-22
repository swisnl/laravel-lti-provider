<?php

return [
    'class-names' => [
        'client' => \Swis\Laravel\LtiProvider\Models\SimpleClient::class,
        'context' => \Swis\Laravel\LtiProvider\Models\Context::class,
        'resource-link' => \Swis\Laravel\LtiProvider\Models\ResourceLink::class,
        'nonce' => \Swis\Laravel\LtiProvider\Models\Nonce::class,
        'user-result' => \Swis\Laravel\LtiProvider\Models\UserResult::class,
        'access-token' => \Swis\Laravel\LtiProvider\Models\AccessToken::class,
    ],
];
