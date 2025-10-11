<?php

namespace App\DataFixtures\CustomFixtures;

use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class TopicFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Use FoxMaster (the admin) as the author of the topic
        $user = $this->getReference('user-foxmaster', User::class);

        $topic = new Topic();
        $topic->setTitle("Les tensions en mer de Chine méridionale et l'équilibre régional");
        $topic->setContent(
            "Depuis plusieurs années, la mer de Chine méridionale est au centre de conflits " .
                "territoriaux impliquant la Chine, le Vietnam, les Philippines et d'autres pays riverains. " .
                "Cette zone stratégique est riche en ressources et cruciale pour le commerce maritime mondial. " .
                "Quels scénarios géopolitiques pourraient émerger dans les 5 prochaines années ?"
        );
        $topic->setUser($user);
        $topic->setCreatedAt(new \DateTimeImmutable());
        $topic->setUpdatedAt(new \DateTimeImmutable());

        $manager->persist($topic);
        $manager->flush();

        // Add a reference to be able to retrieve this topic in other fixtures
        $this->addReference('geopolitics-topic', $topic);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
