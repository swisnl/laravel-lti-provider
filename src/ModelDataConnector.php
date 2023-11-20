<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider;

use ceLTIc\LTI\AccessToken;
use ceLTIc\LTI\Context;
use ceLTIc\LTI\DataConnector\DataConnector;
use ceLTIc\LTI\Enum\IdScope;
use ceLTIc\LTI\Platform;
use ceLTIc\LTI\PlatformNonce;
use ceLTIc\LTI\ResourceLink;
use ceLTIc\LTI\ResourceLinkShareKey;
use ceLTIc\LTI\Tool;
use ceLTIc\LTI\UserResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiClient;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Swis\Laravel\LtiProvider\Models\LtiUserResult;

/** @phpstan-consistent-constructor */
class ModelDataConnector extends DataConnector
{
    protected LtiEnvironment $environment;

    /** @var class-string<Model&LtiClient> */
    protected string $clientClassName;

    public function __construct(LtiEnvironment $environment, string $clientClassName)
    {
        parent::__construct((object) []);

        $this->environment = $environment;
        $this->clientClassName = $clientClassName;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Model&LtiClient>
     */
    protected function getClientBuilder(): Builder
    {
        return $this->clientClassName::query();
    }

    public function loadPlatform(Platform $platform): bool
    {
        if (! empty($platform->getRecordId())) {
            /** @var LtiClient|null $client */
            $client = $this->getClientBuilder()->firstWhere('nr', $platform->getRecordId());
            if (! $client) {
                return false;
            }

            $client->fillLtiPlatform($platform);

            return true;
        }

        if (! empty($platform->platformId) || ! empty($platform->clientId) || ! empty($platform->deploymentId)) {
            $query = $this->getClientBuilder();

            if (! empty($platform->platformId)) {
                $query->where('lti_platform_id', $platform->platformId);
            }
            if (! empty($platform->clientId)) {
                $query->where('lti_client_id', $platform->clientId);
            }
            if (! empty($platform->deploymentId)) {
                $query->where('lti_deployment_id', $platform->deploymentId);
            }

            /** @var LtiClient|null $client */
            $client = $query->first();
            if (! $client) {
                return false;
            }

            $client->fillLtiPlatform($platform);

            return true;
        }

        if (! empty($platform->getKey())) {
            /** @var LtiClient|null $client */
            $client = $this->getClientBuilder()->find($platform->getKey());
            if (! $client) {
                return false;
            }

            $client->fillLtiPlatform($platform);

            return true;
        }

        return false;
    }

    public function savePlatform(Platform $platform): bool
    {
        if (! empty($platform->getRecordId())) {
            /** @var (LtiClient&Model)|null $client */
            $client = $this->getClientBuilder()->firstWhere('nr', $platform->getRecordId());
            if (! $client) {
                return false;
            }

            $this->fixPlatformSettings($platform, true);
            $client->fillFromLtiPlatform($platform);
            $this->fixPlatformSettings($platform, false);
            $client->save();
        } else {
            return false;
        }

        $platform->updated = $client->updated_at->getTimestamp();

        return true;
    }

    public function deletePlatform(Platform $platform): bool
    {
        return false;
    }

    /**
     * @return \ceLTIc\LTI\Platform[]
     */
    public function getPlatforms(): array
    {
        /** @var Collection<int,LtiClient> $clients. */
        $clients = $this->getClientBuilder()->get();

        return $clients->map(function (LtiClient $client) {
            $platform = new Platform($this);
            $client->fillLtiPlatform($platform);
        })->values()->toArray();
    }

    public function loadContext(Context $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiContext|null $ltiContext */
            $ltiContext = $this->environment->contexts()->with('client')->find($context->getRecordId());
            if (! $ltiContext) {
                return false;
            }

            $ltiContext->fillLtiContext($context);

            return true;
        }

        if (! empty($context->ltiContextId)) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiContext|null $ltiContext */
            $ltiContext = $this->environment->contexts()->with('client')->where('external_context_id', $context->ltiContextId)
                ->whereHas('client', function ($query) use ($context) {
                    $query->where('id', $context->getPlatform()->getKey());
                })
                ->first();
            if (! $ltiContext) {
                return false;
            }

            $ltiContext->fillLtiContext($context);

            return true;
        }

        return false;
    }

    public function saveContext(Context $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiContext|null $ltiContext */
            $ltiContext = $this->environment->contexts()->find($context->getRecordId());
            if (! $ltiContext) {
                return false;
            }

            $ltiContext->fillFromLtiContext($context);
            $ltiContext->save();
        } else {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiContext|null $ltiContext */
            $ltiContext = $this->environment->contexts()->make();
            $ltiContext->fillFromLtiContext($context);
            $ltiContext->save();

            $context->setRecordId($ltiContext->id);
            $context->created = $ltiContext->created_at->getTimestamp();
        }

        $context->updated = $ltiContext->updated_at->getTimestamp();

        return true;
    }

    public function deleteContext(Context $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiContext|null $ltiContext */
            $ltiContext = $this->environment->contexts()->find($context->getRecordId());
            if (! $ltiContext) {
                return false;
            }

            $ltiContext->delete();

            return true;
        }

        return false;
    }

    public function loadResourceLink(ResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
            $ltiResourceLink = $this->environment->resourceLinks()->with('client')->find($resourceLink->getRecordId());
            if (! $ltiResourceLink) {
                return false;
            }

            $ltiResourceLink->fillLtiResourceLink($resourceLink);

            return true;
        }

        if (! empty($resourceLink->getContext())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
            $ltiResourceLink = $this->environment->resourceLinks()->with('client')->where('external_resource_link_id', $resourceLink->ltiResourceLinkId)
                ->where(function ($query) use ($resourceLink) {
                    $query
                        ->where('lti_context_id', $resourceLink->getContext()->getRecordId())
                        ->orWhereIn('client_id', function ($query) use ($resourceLink) {
                            $query
                                ->select('client_id')
                                ->from('lti_contexts')
                                ->where('id', $resourceLink->getContext()->getRecordId());
                        });
                })
                ->first();

            if (! $ltiResourceLink) {
                return false;
            }

            $ltiResourceLink->fillLtiResourceLink($resourceLink);

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
        $ltiResourceLink = $this->environment->resourceLinks()->with('client')->where('external_resource_link_id', $resourceLink->ltiResourceLinkId)
            ->where(function ($query) use ($resourceLink) {
                $query
                    ->where('client_id', $resourceLink->getPlatform()->getKey())
                    ->orWhereHas('context', function ($query) use ($resourceLink) {
                        $query->where('client_id', $resourceLink->getPlatform()->getKey());
                    });
            })
            ->first();
        if (! $ltiResourceLink) {
            return false;
        }

        $ltiResourceLink->fillLtiResourceLink($resourceLink);

        return true;
    }

    public function saveResourceLink(ResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
            $ltiResourceLink = $this->environment->resourceLinks()->find($resourceLink->getRecordId());
            if (! $ltiResourceLink) {
                return false;
            }

            $ltiResourceLink->fillFromLtiResourceLink($resourceLink);
            $ltiResourceLink->save();
        } else {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
            $ltiResourceLink = $this->environment->resourceLinks()->make();
            $ltiResourceLink->fillFromLtiResourceLink($resourceLink);
            $ltiResourceLink->save();

            $resourceLink->setRecordId($ltiResourceLink->id);
            $resourceLink->created = $ltiResourceLink->created_at->getTimestamp();
        }

        $resourceLink->updated = $ltiResourceLink->updated_at->getTimestamp();

        return true;
    }

    public function deleteResourceLink(ResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiResourceLink|null $ltiResourceLink */
            $ltiResourceLink = $this->environment->resourceLinks()->find($resourceLink->getRecordId());
            if (! $ltiResourceLink) {
                return false;
            }

            $ltiResourceLink->delete();
            $resourceLink->initialize();

            return true;
        }

        return false;
    }

    /**
     * @return \ceLTIc\LTI\UserResult[]
     */
    public function getUserResultSourcedIDsResourceLink(ResourceLink $resourceLink, bool $localOnly, ?IdScope $idScope): array
    {
        /** @var Collection<int,LtiUserResult> $userResults */
        $userResults = $this->environment->userResults()->where('lti_resource_link_id', $resourceLink->getRecordId())
            ->get()->values();
        $userResults = $userResults->map(function (LtiUserResult $ltiUserResult) use ($resourceLink) {
            $userResult = new UserResult();

            $userResult->setDataConnector($this);
            $userResult->setResourceLinkId($resourceLink->getRecordId());
            $userResult->setResourceLink($resourceLink);

            $ltiUserResult->fillLtiUserResult($userResult);

            return $userResult;
        });

        if (! is_null($idScope)) {
            return $userResults->mapWithKeys(function (UserResult $userResult) use ($idScope) {
                return [$userResult->getId($idScope) => $userResult];
            })->toArray();
        }

        return $userResults->toArray();
    }

    public function getSharesResourceLink(ResourceLink $resourceLink): array
    {
        return [];
    }

    public function loadPlatformNonce(PlatformNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::loadPlatformNonce($nonce);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiNonce|null $ltiNonce */
        $ltiNonce = $this->environment->nonces()->where('client_id', $nonce->getPlatform()->getKey())
            ->where('nonce', $nonce->getValue())
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $ltiNonce) {
            return false;
        }

        $ltiNonce->fillLtiPlatformNonce($nonce);

        return true;
    }

    public function savePlatformNonce(PlatformNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::savePlatformNonce($nonce);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiNonce $ltiNonce */
        $ltiNonce = $this->environment->nonces()->firstOrNew([
            'client_id' => $nonce->getPlatform()->getKey(),
            'nonce' => $nonce->getValue(),
        ]);

        $ltiNonce->fillFromLtiPlatformNonce($nonce);
        $ltiNonce->save();

        return true;
    }

    public function deletePlatformNonce(PlatformNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::deletePlatformNonce($nonce);
        }

        $this->environment->nonces()->where('client_id', $nonce->getPlatform()->getKey())
            ->where('nonce', $nonce->getValue())
            ->delete();

        return true;
    }

    public function loadAccessToken(AccessToken $accessToken): bool
    {
        if (parent::useMemcache()) {
            return parent::loadAccessToken($accessToken);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiAccessToken|null $ltiAccessToken */
        $ltiAccessToken = $this->environment->accessTokens()->where('client_id', $accessToken->getPlatform()->getKey())
            ->first();

        if (! $ltiAccessToken) {
            return false;
        }

        $ltiAccessToken->fillLtiAccessToken($accessToken);

        return true;
    }

    public function saveAccessToken(AccessToken $accessToken): bool
    {
        if (parent::useMemcache()) {
            return parent::saveAccessToken($accessToken);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiAccessToken $ltiAccessToken */
        $ltiAccessToken = $this->environment->accessTokens()->firstOrNew([
            'client_id' => $accessToken->getPlatform()->getKey(),
        ]);
        $ltiAccessToken->fillFromLtiAccessToken($accessToken);
        $ltiAccessToken->save();

        return true;
    }

    public function loadResourceLinkShareKey(ResourceLinkShareKey $shareKey): bool
    {
        return false;
    }

    public function saveResourceLinkShareKey(ResourceLinkShareKey $shareKey): bool
    {
        return false;
    }

    public function deleteResourceLinkShareKey(ResourceLinkShareKey $shareKey): bool
    {
        return false;
    }

    public function loadUserResult(UserResult $userResult): bool
    {
        if (! empty($userResult->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiUserResult|null $ltiUserResult */
            $ltiUserResult = $this->environment->userResults()->find($userResult->getRecordId());
            if (! $ltiUserResult) {
                return false;
            }

            $ltiUserResult->fillLtiUserResult($userResult);

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiUserResult|null $ltiUserResult */
        $ltiUserResult = $this->environment->userResults()->where('lti_resource_link_id', $userResult->getResourceLink()->getRecordId())
            ->where('external_user_id', $userResult->getId(IdScope::IdOnly))
            ->first();
        if (! $ltiUserResult) {
            return false;
        }

        $ltiUserResult->fillLtiUserResult($userResult);

        return true;
    }

    public function saveUserResult(UserResult $userResult): bool
    {
        if (is_null($userResult->created)) {
            /** @var \Swis\Laravel\LtiProvider\Models\LtiUserResult $ltiUserResult */
            $ltiUserResult = $this->environment->userResults()->make();
            $ltiUserResult->fillFromLtiUserResult($userResult);
            $ltiUserResult->save();

            $userResult->setRecordId($ltiUserResult->id);
            $userResult->created = $ltiUserResult->created_at->getTimestamp();
            $userResult->updated = $ltiUserResult->updated_at->getTimestamp();

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\LtiUserResult|null $ltiUserResult */
        $ltiUserResult = $this->environment->userResults()->find($userResult->getRecordId());
        if (! $ltiUserResult) {
            return false;
        }

        $ltiUserResult->fillFromLtiUserResult($userResult);
        $ltiUserResult->save();

        $userResult->updated = $ltiUserResult->updated_at->getTimestamp();

        return true;
    }

    public function deleteUserResult(UserResult $userResult): bool
    {
        /** @var \Swis\Laravel\LtiProvider\Models\LtiUserResult|null $ltiUserResult */
        $ltiUserResult = $this->environment->userResults()->find($userResult->getRecordId());
        if (! $ltiUserResult) {
            return false;
        }

        $ltiUserResult->delete();

        return true;
    }

    public function loadTool(Tool $tool): bool
    {
        throw new \Exception('loadTool not implemented');
    }

    public function saveTool(Tool $tool): bool
    {
        throw new \Exception('saveTool not implemented');
    }

    public function deleteTool(Tool $tool): bool
    {
        throw new \Exception('deleteTool not implemented');
    }

    /**
     * @return \ceLTIc\LTI\Tool[]
     */
    public function getTools(): array
    {
        throw new \Exception('getTools not implemented');
    }

    public static function make(LtiEnvironment $environment): static
    {
        $clientClassName = config('lti-provider.class-names.lti-client');
        if ($clientClassName === '') {
            abort(500, 'please provide an lti client in the lti-provider config');
        }

        if (! class_exists($clientClassName)) {
            abort(500, 'Lti client class does not exist');
        }

        if (! is_subclass_of($clientClassName, Model::class)) {
            abort(500, 'Lti client class must be a subclass of '.Model::class);
        }

        if (! is_subclass_of($clientClassName, LtiClient::class)) {
            abort(500, 'Lti client class must be an implementation of '.LtiClient::class);
        }

        return new static($environment, $clientClassName);
    }
}
