<?php

namespace App\DataFixtures\CustomFixtures;

use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {

        $topic = $this->getReference('geopolitics-topic', Topic::class);


        $commentsData = [
            [
                "content" => "La lecture néoréaliste de cette crise est d'une naïveté confondante. On parle ici de guerre hybride "
                    . "asymétrique dans un espace contesté où la doctrine A2/AD chinoise redéfinit complètement les rapports de force. "
                    . "Mais bon, continuons à analyser ça avec des concepts des années 50 si ça vous amuse.",
                "user" => $this->getReference('user-spotty123', User::class),
            ],
            [
                "content" => "Avec tout le respect que je vous dois, réduire la posture chinoise à de \"l'asymétrie hybride\" "
                    . "démontre une méconnaissance flagrante du lawfare et du soft power normatif. La Chine construit un ordre "
                    . "juridique alternatif via les SLOC et le contrôle des détroits. Ce n'est pas de la \"guerre hybride\", "
                    . "c'est de la redéfinition hégémonique du droit international maritime.",
                "user" => $this->getReference('user-bob', User::class),
            ],
            [
                "content" => "Pardon mais... QUOI ? 😤 Le \"lawfare\" ? Sérieusement ? On nage en plein constructivisme "
                    . "post-moderne là ! Les îles Spratley sont des enjeux géoéconomiques CONCRETS : hydrocarbures, terres rares, "
                    . "routes commerciales. Pendant qu'on discute de \"soft power normatif\", la Chine bétonne des pistes "
                    . "d'atterrissage et militarise la zone. Réveillez-vous !",
                "user" => $this->getReference('user-ladymeadow', User::class),
            ],
            [
                "content" => "Je trouve assez savoureux qu'on me parle de \"concret\" quand l'analyse géostratégique proposée ici "
                    . "relève du café du commerce. Les installations militaires chinoises s'inscrivent précisément dans une logique "
                    . "de déni d'accès (A2/AD) couplée à une projection normative via les tribunaux internationaux. Mais je suppose "
                    . "que citer Mearsheimer ou Kissinger, c'est trop \"post-moderne\" pour certains esprits pragmatiques.",
                "user" => $this->getReference('user-spotty123', User::class),
            ],
            [
                "content" => "Hello!",
                "user" => $this->getReference('user-ch33kpouch47', User::class),
            ],
        ];

        $topicCreatedAt = $topic->getCreatedAt();
        $currentDate = clone $topicCreatedAt;
        foreach ($commentsData as $i => $data) {
            $randMinutes = random_int(5, 60);
            $currentDate = (clone $currentDate)->add(new \DateInterval("PT{$randMinutes}M"));

            $comment = (new Comment())
                ->setContent($data["content"])
                ->setUser($data["user"])
                ->setTopic($topic)
                ->setCreatedAt($currentDate)
                ->setUpdatedAt($currentDate);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            TopicFixtures::class,
        ];
    }
}
