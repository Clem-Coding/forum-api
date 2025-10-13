<?php

// src/State/UserMeProvider.php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;

final class UserMeProvider implements ProviderInterface
{
    public function __construct(private SecurityBundleSecurity $security) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Aucun utilisateur connecté ou token invalide.');
        }

        return $user;
    }
}
