<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Topic;
use App\Factory\TopicFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TopicTest extends ApiTestCase
{
    use ResetDatabase, Factories;

    private function createAuthenticatedUser(): array
    {
        $user = UserFactory::createOne([
            'username' => 'testuser',
            'plainPassword' => 'Test@Password123',
        ]);

        $token = self::getContainer()
            ->get('lexik_jwt_authentication.jwt_manager')
            ->create($user);

        return [$user, $token];
    }

    public function testGetCollection(): void
    {
        TopicFactory::createMany(23);

        $response = static::createClient()->request('GET', '/api/topics');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Topic',
            '@id' => '/api/topics',
            '@type' => 'Collection',
            'totalItems' => 23,
        ]);

        $this->assertMatchesResourceCollectionJsonSchema(Topic::class);
    }

    public function testCreateTopic(): void
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = static::createClient()->request('POST', '/api/topics', [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'json' => [
                'title' => 'Nouveau sujet de test',
                'content' => 'Contenu du sujet de test',
                'user' => '/api/users/' . $user->getId(),
            ],
        ]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'title' => 'Nouveau sujet de test',
            'content' => 'Contenu du sujet de test',
            'user' => [
                'username' => $user->getUsername(),
            ],
        ]);

        $this->assertMatchesResourceItemJsonSchema(Topic::class);
    }



    public function testUpdateTopic(): void
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $topic = TopicFactory::createOne(['user' => $user]);

        $client = static::createClient();

        $response = $client->request('PATCH', '/api/topics/' . $topic->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/merge-patch+json',
                'Accept' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Updated Topic Title',
                'content' => 'Updated content of the topic.',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'title' => 'Updated Topic Title',
        ]);
    }



    public function testDeleteTopic(): void
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $topic = TopicFactory::createOne(['user' => $user]);

        $response = static::createClient()->request('DELETE', '/api/topics/' . $topic->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Topic::class)->find($topic->getId())
        );
    }
}
