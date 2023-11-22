<?php

namespace Tests\ModelDataConnector;

use ceLTIc\LTI\AccessToken;
use ceLTIc\LTI\Context;
use ceLTIc\LTI\Enum\IdScope;
use ceLTIc\LTI\Platform;
use ceLTIc\LTI\PlatformNonce;
use ceLTIc\LTI\ResourceLink;
use ceLTIc\LTI\ResourceLinkShareKey;
use ceLTIc\LTI\UserResult;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Swis\Laravel\LtiProvider\Models\Contracts\LtiClient;
use Swis\Laravel\LtiProvider\Models\SimpleClient;

trait ModelDataConnectorTests
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function createClient(array $attributes = []): LtiClient&Model
    {
        /** @var LtiClient&Model $client */
        $client = Factory::factoryForModel(config('lti-provider.class-names.lti-client'))->create($attributes);

        return $client;
    }

    /** @test */
    public function it_should_load_platform(): void
    {
        // Arrange
        $client = $this->createClient();

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);

        // Assert
        $this->assertEquals($client->name, $platform->name);
    }

    /** @test */
    public function it_should_update_platform(): void
    {
        // Arrange
        $originalName = 'Foobar';
        $client = $this->createClient([
            'name' => $originalName,
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $platform->name = 'Barfoo';
        $platform->setSetting('a', 'b');
        $platform->save();

        $reloadedPlatform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);

        // Assert
        $this->assertEquals($originalName, $reloadedPlatform->name);
        $this->assertEquals('b', $reloadedPlatform->getSetting('a'));
    }

    /** @test */
    public function it_should_not_insert_platform(): void
    {
        // Arrange
        $platform = new Platform($this->connector);

        // Act
        $ok = $platform->save();

        // Assert
        $this->assertFalse($ok);
        $this->assertEmpty(SimpleClient::all());
    }

    /** @test */
    public function it_should_load_context_from_external_context_id(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);

        // Assert
        $this->assertEquals($ltiContext->getRecordId(), $context->id);
    }

    /** @test */
    public function it_should_insert_context(): void
    {
        // Arrange
        $client = $this->createClient();

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, '123');
        $ltiContext->title = 'Barfoo';
        $ltiContext->save();

        // Assert
        $contexts = $client->contexts()->get();
        $this->assertCount(1, $contexts);
    }

    /** @test */
    public function it_should_update_context(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);

        $ltiContext->setSetting('a', 'b');
        $ltiContext->save();

        $context->refresh();

        // Assert
        $this->assertEquals('b', $context->settings['a']);
    }

    /** @test */
    public function it_should_delete_context(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);

        $ltiContext->delete();

        // Assert
        $this->expectException(ModelNotFoundException::class);
        $context->refresh();
    }

    /** @test */
    public function it_should_load_resource_link_from_record_id(): void
    {
        // Arrange
        $client = $this->createClient();

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'client_id' => $client->getKey(),
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $ltiResourceLink = ResourceLink::fromRecordId($resourceLink->id, $this->connector);

        // Assert
        $this->assertEquals($ltiResourceLink->ltiResourceLinkId, $resourceLink->external_resource_link_id);
    }

    /** @test */
    public function it_should_load_resource_link_from_external_resource_link_id_without_context(): void
    {
        // Arrange
        $client = $this->createClient();

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'client_id' => $client->getKey(),
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiResourceLink = ResourceLink::fromPlatform($platform, $resourceLink->external_resource_link_id);

        // Assert
        $this->assertEquals($ltiResourceLink->title, $resourceLink->title);
    }

    /** @test */
    public function it_should_insert_resource_link_without_context(): void
    {
        // Arrange
        $client = $this->createClient();

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiResourceLink = ResourceLink::fromPlatform($platform, '123');
        $ltiResourceLink->title = 'Barfoo';
        $ltiResourceLink->save();

        // Assert
        $resourceLinks = $client->resourceLinks()->get();
        $this->assertCount(1, $resourceLinks);
    }

    /** @test */
    public function it_should_load_resource_link_from_external_resource_link_id_with_context(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Baz',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($ltiContext, $resourceLink->external_resource_link_id);

        // Assert
        $this->assertEquals($ltiResourceLink->title, $resourceLink->title);
    }

    /** @test */
    public function it_should_insert_resource_link_with_context(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($ltiContext, '123');
        $ltiResourceLink->title = 'Barfoo';
        $ltiResourceLink->save();

        // Assert
        $contextResourceLinks = $context->resourceLinks()->get();
        $this->assertCount(1, $contextResourceLinks);

        $clientResourceLinks = $client->resourceLinks()->get();
        $this->assertCount(1, $clientResourceLinks);
    }

    /** @test */
    public function it_should_delete_resource_link(): void
    {
        // Arrange
        $client = $this->createClient();

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'client_id' => $client->getKey(),
            'external_resource_link_id' => '123',
            'title' => 'Baz',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiResourceLink = ResourceLink::fromPlatform($platform, $resourceLink->external_resource_link_id);

        $ltiResourceLink->delete();

        // Assert
        $this->expectException(ModelNotFoundException::class);
        $resourceLink->refresh();
    }

    /** @test */
    public function it_should_get_user_results_for_resource_link(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink1 = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);
        $resourceLink2 = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '456',
            'title' => 'Barfoo',
        ]);

        $userResult1 = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink1->id,
            'external_user_result_id' => '111',
            'external_user_id' => 'aaa',
        ]);
        $userResult2 = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink1->id,
            'external_user_result_id' => '222',
            'external_user_id' => 'bbb',
        ]);
        $userResult3 = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink2->id,
            'external_user_result_id' => '333',
            'external_user_id' => 'ccc',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiResourceLink1 = ResourceLink::fromPlatform($platform, $resourceLink1->external_resource_link_id);
        $ltiResourceLink2 = ResourceLink::fromPlatform($platform, $resourceLink2->external_resource_link_id);

        $userResults1 = $ltiResourceLink1->getUserResultSourcedIDs(false, IdScope::Platform);
        $userResults2 = $ltiResourceLink2->getUserResultSourcedIDs();

        // Assert
        $this->assertCount(2, $userResults1);
        $key = $client->getLtiKey().IdScope::SEPARATOR.$userResult1->external_user_id;
        $this->assertArrayHasKey($key, $userResults1);

        $this->assertCount(1, $userResults2);
    }

    /** @test */
    public function it_should_never_return_shares_for_a_result_link(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $context = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($context, $resourceLink->external_resource_link_id);

        $shares = $ltiResourceLink->getShares();

        // Assert
        $this->assertCount(0, $shares);
    }

    /** @test */
    public function it_should_load_user_result_from_record_id(): void
    {
        // Arrange
        $client = $this->createClient();

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'client_id' => $client->getKey(),
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);
        $userResult = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink->id,
            'external_user_result_id' => '123',
            'external_user_id' => '456',
        ]);

        // Act
        $ltiUserResult = UserResult::fromRecordId($userResult->id, $this->connector);

        // Assert
        $this->assertEquals($ltiUserResult->ltiUserId, $userResult->external_user_id);
    }

    /** @test */
    public function it_should_load_nonce(): void
    {
        // Arrange
        $client = $this->createClient();

        $nonce = $this->ltiEnvironment->nonces()->create([
            'client_id' => $client->getKey(),
            'nonce' => '123',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiNonce = new PlatformNonce($platform, $nonce->nonce);
        $this->connector->loadPlatformNonce($ltiNonce);

        // Assert
        $this->assertEquals($nonce->expires_at->getTimestamp(), $ltiNonce->expires);
    }

    /** @test */
    public function it_should_insert_nonce(): void
    {
        // Arrange
        $client = $this->createClient();

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiNonce = new PlatformNonce($platform, '123');
        $ltiNonce->save();

        // Assert
        $this->assertDatabaseHas('lti_nonces', [
            'nonce' => '123',
        ]);
    }

    /** @test */
    public function it_should_update_nonce(): void
    {
        // Arrange
        $client = $this->createClient();

        $nonce = $this->ltiEnvironment->nonces()->create([
            'client_id' => $client->getKey(),
            'nonce' => '123',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiNonce = new PlatformNonce($platform, $nonce->nonce);
        $ltiNonce->save();

        // Assert
        $nonce->refresh();
        $this->assertGreaterThan(Carbon::now()->addMinutes(PlatformNonce::MAX_NONCE_AGE - 5), $nonce->expires_at);
    }

    /** @test */
    public function it_should_delete_nonce(): void
    {
        // Arrange
        $client = $this->createClient();

        $nonce = $this->ltiEnvironment->nonces()->create([
            'client_id' => $client->getKey(),
            'nonce' => '123',
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiNonce = new PlatformNonce($platform, $nonce->nonce);
        $ltiNonce->delete();

        // Assert
        $this->assertDatabaseMissing('lti_nonces', [
            'nonce' => '123',
        ]);
    }

    /** @test */
    public function it_should_load_access_token(): void
    {
        // Arrange
        $client = $this->createClient();

        $accessToken = $this->ltiEnvironment->accessTokens()->create([
            'client_id' => $client->getKey(),
            'access_token' => '123',
            'scopes' => ['foo', 'bar'],
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiAccessToken = new AccessToken($platform);

        // Assert
        $this->assertEquals($ltiAccessToken->token, $accessToken->access_token);
    }

    /** @test */
    public function it_should_insert_access_token(): void
    {
        // Arrange
        $client = $this->createClient();

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiAccessToken = new AccessToken($platform, ['foo', 'bar'], '123', 5 * 60);
        $ltiAccessToken->save();

        // Assert
        $this->assertDatabaseHas('lti_access_tokens', [
            'access_token' => $ltiAccessToken->token,
        ]);
    }

    /** @test */
    public function it_should_update_access_token(): void
    {
        // Arrange
        $client = $this->createClient();

        $accessToken = $this->ltiEnvironment->accessTokens()->create([
            'client_id' => $client->getKey(),
            'access_token' => '123',
            'scopes' => ['foo', 'bar'],
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiAccessToken = new AccessToken($platform, ['foo', 'bar'], '456', 5 * 60);
        $ltiAccessToken->save();

        // Assert
        $accessToken->refresh();
        $this->assertEquals('456', $accessToken->access_token);
    }

    /** @test */
    public function it_should_never_load_resource_link_share_key(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $context = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($context, $resourceLink->external_resource_link_id);
        $shareKey = new ResourceLinkShareKey($ltiResourceLink);

        $ok = $this->connector->loadResourceLinkShareKey($shareKey);

        // Assert
        $this->assertFalse($ok);
    }

    /** @test */
    public function it_should_never_save_resource_link_share_key(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $context = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($context, $resourceLink->external_resource_link_id);
        $shareKey = new ResourceLinkShareKey($ltiResourceLink);

        $ok = $shareKey->save();

        // Assert
        $this->assertFalse($ok);
    }

    public function it_should_never_delete_resource_link_share_key(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $context = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($context, $resourceLink->external_resource_link_id);
        $shareKey = new ResourceLinkShareKey($ltiResourceLink);

        $ok = $shareKey->delete();

        // Assert
        $this->assertFalse($ok);
    }

    /** @test */
    public function it_should_load_user_result_from_user_id(): void
    {
        // Arrange
        $client = $this->createClient();

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'client_id' => $client->getKey(),
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        $userResult = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink->id,
            'external_user_result_id' => '123',
            'external_user_id' => '456',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiResourceLink = ResourceLink::fromPlatform($platform, $resourceLink->external_resource_link_id);
        $ltiUserResult = UserResult::fromResourceLink($ltiResourceLink, $userResult->external_user_id);

        // Assert
        $this->assertEquals($ltiUserResult->getRecordId(), $userResult->id);
    }

    /** @test */
    public function it_should_insert_user_result(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($ltiContext, $resourceLink->external_resource_link_id);
        $ltiUserResult = new UserResult();
        $ltiUserResult->setDataConnector($this->connector);
        $ltiUserResult->setResourceLinkId($ltiResourceLink->getRecordId());
        $ltiUserResult->ltiUserId = '456';
        $ltiUserResult->ltiResultSourcedId = '789';

        $ltiUserResult->save();

        // Assert
        $this->assertNotNull($ltiUserResult->getRecordId());
        $this->assertDatabaseHas('lti_user_results', [
            'id' => $ltiUserResult->getRecordId(),
            'external_user_result_id' => '789',
            'external_user_id' => '456',
            'lti_resource_link_id' => $resourceLink->id,
        ]);
    }

    /** @test */
    public function it_should_update_user_result(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        $userResult = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink->id,
            'external_user_result_id' => '123',
            'external_user_id' => '456',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($ltiContext, $resourceLink->external_resource_link_id);
        $ltiUserResult = UserResult::fromResourceLink($ltiResourceLink, $userResult->external_user_id);

        $ltiUserResult->ltiResultSourcedId = '789';
        $ltiUserResult->save();

        // Assert
        $this->assertDatabaseHas('lti_user_results', [
            'id' => $ltiUserResult->getRecordId(),
            'external_user_result_id' => '789',
            'external_user_id' => '456',
            'lti_resource_link_id' => $ltiResourceLink->getRecordId(),
        ]);
    }

    /** @test */
    public function it_should_delete_user_result(): void
    {
        // Arrange
        $client = $this->createClient();

        $context = $this->ltiEnvironment->contexts()->create([
            'client_id' => $client->getKey(),
            'external_context_id' => '123',
            'title' => 'Barfoo',
        ]);

        $resourceLink = $this->ltiEnvironment->resourceLinks()->create([
            'lti_context_id' => $context->id,
            'external_resource_link_id' => '123',
            'title' => 'Barfoo',
        ]);

        $userResult = $this->ltiEnvironment->userResults()->create([
            'lti_resource_link_id' => $resourceLink->id,
            'external_user_result_id' => '123',
            'external_user_id' => '456',
        ]);

        // Act
        $platform = Platform::fromRecordId($client->getLtiRecordId(), $this->connector);
        $ltiContext = Context::fromPlatform($platform, $context->external_context_id);
        $ltiResourceLink = ResourceLink::fromContext($ltiContext, $resourceLink->external_resource_link_id);
        $ltiUserResult = UserResult::fromResourceLink($ltiResourceLink, $userResult->external_user_id);

        $ok = $ltiUserResult->delete();

        // Assert
        $this->assertTrue($ok);
        $this->expectException(ModelNotFoundException::class);
        $userResult->refresh();
    }
}
