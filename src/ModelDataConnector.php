<?php

declare(strict_types=1);

namespace Swis\Laravel\LtiProvider;

use ceLTIc\LTI\AccessToken as CelticAccessToken;
use ceLTIc\LTI\Context as CelticContext;
use ceLTIc\LTI\DataConnector\DataConnector;
use ceLTIc\LTI\Enum\IdScope;
use ceLTIc\LTI\Platform as CelticPlatform;
use ceLTIc\LTI\PlatformNonce as CelticNonce;
use ceLTIc\LTI\ResourceLink as CelticResourceLink;
use ceLTIc\LTI\ResourceLinkShareKey;
use ceLTIc\LTI\Tool;
use ceLTIc\LTI\UserResult as CelticUserResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Swis\Laravel\LtiProvider\Models\Contracts\Client;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiEnvironment;
use Swis\Laravel\LtiProvider\Models\UserResult;

/** @phpstan-consistent-constructor */
class ModelDataConnector extends DataConnector
{
    protected LtiEnvironment $environment;

    /**
     * @var class-string<\Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\Client>
     */
    protected string $clientClassName;

    public function __construct(LtiEnvironment $environment, string $clientClassName)
    {
        parent::__construct((object) []);

        $this->environment = $environment;
        $this->clientClassName = $clientClassName;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model&\Swis\Laravel\LtiProvider\Models\Contracts\Client>
     */
    protected function getClientBuilder(): Builder
    {
        /* @phpstan-ignore-next-line */
        return $this->clientClassName::query();
    }

    protected function getClientLtiRecordIdColumn(): string
    {
        return $this->clientClassName::getLtiRecordIdColumn();
    }

    protected function getClientLtiKeyColumn(): string
    {
        return $this->clientClassName::getLtiKeyColumn();
    }

    protected function getClientForeignKeyFromPlatform(CelticPlatform $platform): int|string
    {
        return $this->clientClassName::getForeignKeyFromPlatform($platform);
    }

    public function loadPlatform(CelticPlatform $platform): bool
    {
        if (! empty($platform->getRecordId())) {
            /** @var Client|null $clientModel */
            $clientModel = $this->getClientBuilder()->firstWhere($this->getClientLtiRecordIdColumn(), $platform->getRecordId());
            if (! $clientModel) {
                return false;
            }

            $clientModel->fillLtiPlatform($platform);

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

            /** @var Client|null $clientModel */
            $clientModel = $query->first();
            if (! $clientModel) {
                return false;
            }

            $clientModel->fillLtiPlatform($platform);

            return true;
        }

        if (! empty($platform->getKey())) {
            /** @var Client|null $clientModel */
            $clientModel = $this->getClientBuilder()->firstWhere($this->getClientLtiKeyColumn(), $platform->getKey());
            if (! $clientModel) {
                return false;
            }

            $clientModel->fillLtiPlatform($platform);

            return true;
        }

        return false;
    }

    public function savePlatform(CelticPlatform $platform): bool
    {
        if (! empty($platform->getRecordId())) {
            /** @var (Client&Model)|null $clientModel */
            $clientModel = $this->getClientBuilder()->firstWhere($this->getClientLtiRecordIdColumn(), $platform->getRecordId());
            if (! $clientModel) {
                return false;
            }

            $this->fixPlatformSettings($platform, true);
            $clientModel->fillFromLtiPlatform($platform);
            $this->fixPlatformSettings($platform, false);
            $clientModel->save();
        } else {
            return false;
        }

        $platform->updated = $clientModel->getAttribute($clientModel->getUpdatedAtColumn())->getTimestamp();

        return true;
    }

    public function deletePlatform(CelticPlatform $platform): bool
    {
        return false;
    }

    /**
     * @return \ceLTIc\LTI\Platform[]
     */
    public function getPlatforms(): array
    {
        /** @var Collection<int,Client> $clientModels */
        $clientModels = $this->getClientBuilder()->get();

        return $clientModels->map(function (Client $clientModel) {
            $platform = new CelticPlatform($this);
            $clientModel->fillLtiPlatform($platform);
        })->values()->toArray();
    }

    public function loadContext(CelticContext $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\Context|null $contextModel */
            $contextModel = $this->environment->contexts()
                ->with('client')
                ->find($context->getRecordId());
            if (! $contextModel) {
                return false;
            }

            $contextModel->fillLtiContext($context);

            return true;
        }

        if (! empty($context->ltiContextId)) {
            /** @var \Swis\Laravel\LtiProvider\Models\Context|null $contextModel */
            $contextModel = $this->environment->contexts()
                ->with('client')
                ->where('external_context_id', $context->ltiContextId)
                ->where('client_id', $this->getClientForeignKeyFromPlatform($context->getPlatform()))
                ->first();
            if (! $contextModel) {
                return false;
            }

            $contextModel->fillLtiContext($context);

            return true;
        }

        return false;
    }

    public function saveContext(CelticContext $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\Context|null $contextModel */
            $contextModel = $this->environment->contexts()->find($context->getRecordId());
            if (! $contextModel) {
                return false;
            }

            $contextModel->fillFromLtiContext($context);
            $contextModel->save();
        } else {
            /** @var \Swis\Laravel\LtiProvider\Models\Context|null $contextModel */
            $contextModel = $this->environment->contexts()->make();
            $contextModel->fillFromLtiContext($context);
            $contextModel->save();

            $context->setRecordId($contextModel->id);
            $context->created = $contextModel->created_at->getTimestamp();
        }

        $context->updated = $contextModel->updated_at->getTimestamp();

        return true;
    }

    public function deleteContext(CelticContext $context): bool
    {
        if (! empty($context->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\Context|null $contextModel */
            $contextModel = $this->environment->contexts()->find($context->getRecordId());
            if (! $contextModel) {
                return false;
            }

            $contextModel->delete();

            return true;
        }

        return false;
    }

    public function loadResourceLink(CelticResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
            $resourceLinkModel = $this->environment->resourceLinks()
                ->with('client')
                ->find($resourceLink->getRecordId());
            if (! $resourceLinkModel) {
                return false;
            }

            $resourceLinkModel->fillLtiResourceLink($resourceLink);

            return true;
        }

        if (! empty($resourceLink->getContext())) {
            /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
            $resourceLinkModel = $this->environment->resourceLinks()
                ->with('client')
                ->where('external_resource_link_id', $resourceLink->ltiResourceLinkId)
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
            if (! $resourceLinkModel) {
                return false;
            }

            $resourceLinkModel->fillLtiResourceLink($resourceLink);

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
        $resourceLinkModel = $this->environment->resourceLinks()
            ->with('client')
            ->where('external_resource_link_id', $resourceLink->ltiResourceLinkId)
            ->where(function ($query) use ($resourceLink) {
                $query
                    ->where('client_id', $this->getClientForeignKeyFromPlatform($resourceLink->getPlatform()))
                    ->orWhereHas('context', function ($query) use ($resourceLink) {
                        $query->where('client_id', $this->getClientForeignKeyFromPlatform($resourceLink->getPlatform()));
                    });
            })
            ->first();
        if (! $resourceLinkModel) {
            return false;
        }

        $resourceLinkModel->fillLtiResourceLink($resourceLink);

        return true;
    }

    public function saveResourceLink(CelticResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
            $resourceLinkModel = $this->environment->resourceLinks()->find($resourceLink->getRecordId());
            if (! $resourceLinkModel) {
                return false;
            }

            $resourceLinkModel->fillFromLtiResourceLink($resourceLink);
            $resourceLinkModel->save();
        } else {
            /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
            $resourceLinkModel = $this->environment->resourceLinks()->make();
            $resourceLinkModel->fillFromLtiResourceLink($resourceLink);
            $resourceLinkModel->save();

            $resourceLink->setRecordId($resourceLinkModel->id);
            $resourceLink->created = $resourceLinkModel->created_at->getTimestamp();
        }

        $resourceLink->updated = $resourceLinkModel->updated_at->getTimestamp();

        return true;
    }

    public function deleteResourceLink(CelticResourceLink $resourceLink): bool
    {
        if (! empty($resourceLink->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\ResourceLink|null $resourceLinkModel */
            $resourceLinkModel = $this->environment->resourceLinks()->find($resourceLink->getRecordId());
            if (! $resourceLinkModel) {
                return false;
            }

            $resourceLinkModel->delete();
            $resourceLink->initialize();

            return true;
        }

        return false;
    }

    /**
     * @return \ceLTIc\LTI\UserResult[]
     */
    public function getUserResultSourcedIDsResourceLink(CelticResourceLink $resourceLink, bool $localOnly, ?IdScope $idScope): array
    {
        /** @var Collection<int,UserResult> $userResultModels */
        $userResultModels = $this->environment->userResults()
            ->where('lti_resource_link_id', $resourceLink->getRecordId())
            ->get()
            ->values();

        /** @var Collection<int,CelticUserResult> $userResults */
        $userResults = $userResultModels->map(function (UserResult $userResultModel) use ($resourceLink): CelticUserResult {
            $userResult = new CelticUserResult;

            $userResult->setDataConnector($this);
            $userResult->setResourceLinkId($resourceLink->getRecordId());
            $userResult->setResourceLink($resourceLink);

            $userResultModel->fillLtiUserResult($userResult);

            return $userResult;
        });

        if (! is_null($idScope)) {
            return $userResults->mapWithKeys(function (CelticUserResult $userResult) use ($idScope) {
                return [$userResult->getId($idScope) => $userResult];
            })->toArray();
        }

        return $userResults->toArray();
    }

    public function getSharesResourceLink(CelticResourceLink $resourceLink): array
    {
        return [];
    }

    public function loadPlatformNonce(CelticNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::loadPlatformNonce($nonce);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\Nonce|null $nonceModel */
        $nonceModel = $this->environment->nonces()
            ->where('client_id', $this->getClientForeignKeyFromPlatform($nonce->getPlatform()))
            ->where('nonce', $nonce->getValue())
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $nonceModel) {
            return false;
        }

        $nonceModel->fillLtiPlatformNonce($nonce);

        return true;
    }

    public function savePlatformNonce(CelticNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::savePlatformNonce($nonce);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\Nonce $nonceModel */
        $nonceModel = $this->environment->nonces()->firstOrNew([
            'client_id' => $this->getClientForeignKeyFromPlatform($nonce->getPlatform()),
            'nonce' => $nonce->getValue(),
        ]);

        $nonceModel->fillFromLtiPlatformNonce($nonce);
        $nonceModel->save();

        return true;
    }

    public function deletePlatformNonce(CelticNonce $nonce): bool
    {
        if (parent::useMemcache()) {
            return parent::deletePlatformNonce($nonce);
        }

        $this->environment->nonces()
            ->where('client_id', $this->getClientForeignKeyFromPlatform($nonce->getPlatform()))
            ->where('nonce', $nonce->getValue())
            ->delete();

        return true;
    }

    public function loadAccessToken(CelticAccessToken $accessToken): bool
    {
        if (parent::useMemcache()) {
            return parent::loadAccessToken($accessToken);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\AccessToken|null $accessTokenModel */
        $accessTokenModel = $this->environment->accessTokens()
            ->where('client_id', $this->getClientForeignKeyFromPlatform($accessToken->getPlatform()))
            ->first();
        if (! $accessTokenModel) {
            return false;
        }

        $accessTokenModel->fillLtiAccessToken($accessToken);

        return true;
    }

    public function saveAccessToken(CelticAccessToken $accessToken): bool
    {
        if (parent::useMemcache()) {
            return parent::saveAccessToken($accessToken);
        }

        /** @var \Swis\Laravel\LtiProvider\Models\AccessToken $accessTokenModel */
        $accessTokenModel = $this->environment->accessTokens()->firstOrNew([
            'client_id' => $this->getClientForeignKeyFromPlatform($accessToken->getPlatform()),
        ]);
        $accessTokenModel->fillFromLtiAccessToken($accessToken);
        $accessTokenModel->save();

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

    public function loadUserResult(CelticUserResult $userResult): bool
    {
        if (! empty($userResult->getRecordId())) {
            /** @var \Swis\Laravel\LtiProvider\Models\UserResult|null $userResultModel */
            $userResultModel = $this->environment->userResults()->find($userResult->getRecordId());
            if (! $userResultModel) {
                return false;
            }

            $userResultModel->fillLtiUserResult($userResult);

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\UserResult|null $userResultModel */
        $userResultModel = $this->environment->userResults()
            ->where('lti_resource_link_id', $userResult->getResourceLink()->getRecordId())
            ->where('external_user_id', $userResult->getId(IdScope::IdOnly))
            ->first();
        if (! $userResultModel) {
            return false;
        }

        $userResultModel->fillLtiUserResult($userResult);

        return true;
    }

    public function saveUserResult(CelticUserResult $userResult): bool
    {
        if (is_null($userResult->created)) {
            /** @var \Swis\Laravel\LtiProvider\Models\UserResult $userResultModel */
            $userResultModel = $this->environment->userResults()->make();
            $userResultModel->fillFromLtiUserResult($userResult);
            $userResultModel->save();

            $userResult->setRecordId($userResultModel->id);
            $userResult->created = $userResultModel->created_at->getTimestamp();
            $userResult->updated = $userResultModel->updated_at->getTimestamp();

            return true;
        }

        /** @var \Swis\Laravel\LtiProvider\Models\UserResult|null $userResultModel */
        $userResultModel = $this->environment->userResults()->find($userResult->getRecordId());
        if (! $userResultModel) {
            return false;
        }

        $userResultModel->fillFromLtiUserResult($userResult);
        $userResultModel->save();

        $userResult->updated = $userResultModel->updated_at->getTimestamp();

        return true;
    }

    public function deleteUserResult(CelticUserResult $userResult): bool
    {
        /** @var \Swis\Laravel\LtiProvider\Models\UserResult|null $userResultModel */
        $userResultModel = $this->environment->userResults()->find($userResult->getRecordId());
        if (! $userResultModel) {
            return false;
        }

        $userResultModel->delete();

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
        $clientClassName = config('lti-provider.class-names.client');
        if ($clientClassName === '') {
            abort(500, 'please provide an lti client in the lti-provider config');
        }

        if (! class_exists($clientClassName)) {
            abort(500, 'Lti client class does not exist');
        }

        if (! is_subclass_of($clientClassName, Model::class)) {
            abort(500, 'Lti client class must be a subclass of '.Model::class);
        }

        if (! is_subclass_of($clientClassName, Client::class)) {
            abort(500, 'Lti client class must be an implementation of '.Client::class);
        }

        return new static($environment, $clientClassName);
    }
}
