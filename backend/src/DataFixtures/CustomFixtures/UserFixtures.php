<?php

namespace App\DataFixtures\CustomFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'username' => 'FoxMaster',
                'roles' => ['ROLE_ADMIN'],
                'avatarUrl' => '/assets/avatars/avatar-foxmaster.svg',
                'reference' => 'user-foxmaster',
            ],
            [
                'username' => 'Spotty123',
                'roles' => [],
                'avatarUrl' => '/assets/avatars/avatar-spotty123.svg',
                'reference' => 'user-spotty123',
            ],
            [
                'username' => 'Bob',
                'roles' => [],
                'avatarUrl' => '/assets/avatars/avatar-bob.svg',
                'reference' => 'user-bob',
            ],
            [
                'username' => 'Lady-Meadow',
                'roles' => [],
                'avatarUrl' => '/assets/avatars/avatar-ladymeadow.svg',
                'reference' => 'user-ladymeadow',
            ],
            [
                'username' => 'Ch33kPouch47',
                'roles' => [],
                'avatarUrl' => '/assets/avatars/avatar-ch33kpouch47.svg',
                'reference' => 'user-ch33kpouch47',
            ],
        ];

        foreach ($usersData as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setRoles($data['roles']);
            $user->setAvatarUrl($data['avatarUrl']);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, 'password')
            );

            $manager->persist($user);

            // Add a reference for each user
            $this->addReference($data['reference'], $user);
        }

        $manager->flush();
    }
}
