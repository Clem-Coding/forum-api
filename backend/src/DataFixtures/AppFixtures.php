<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use App\Factory\TopicFactory;
use App\Factory\CommentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create exactly 30 users with a specific avatar for each
        $users = [];
        for ($i = 1; $i <= 30; $i++) {
            $users[] = UserFactory::createOne([
                'avatarUrl' => sprintf("/assets/avatars/50-Animal-Avatar-Icons-%02d.svg", $i)
            ]);
        }

        $topics = TopicFactory::createMany(15, function () use ($users) {
            return [
                'user' => $users[array_rand($users)]
            ];
        });

        // For each topic, create between 4 and 30 comments with a random date after the topic's creation date
        foreach ($topics as $topic) {
            $topicCreatedAt = $topic->getCreatedAt();
            $commentsCount = random_int(4, 30);

            CommentFactory::createMany($commentsCount, function () use ($topic, $topicCreatedAt, $users) {
                // Create a comment date after the topic (between 1 hour and 30 days later)
                $hoursAfterTopic = random_int(1, 24 * 30);
                $commentTimestamp = $topicCreatedAt->getTimestamp() + ($hoursAfterTopic * 3600);
                $commentDate = new \DateTimeImmutable('@' . $commentTimestamp);

                return [
                    'topic' => $topic,
                    'user' => $users[array_rand($users)],
                    'createdAt' => $commentDate,
                    'updatedAt' => $commentDate,
                ];
            });
        }
    }
}
