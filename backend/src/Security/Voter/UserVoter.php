<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserVoter extends Voter
{
    public const VIEW   = 'USER_VIEW';
    public const EDIT   = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        // user is not logged in
        if (!$currentUser instanceof User) {
            return false;
        }

        // admin can do anything
        if (in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return true;
        }

        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                // l’utilisateur peut agir uniquement sur son propre compte
                return $user->getId() === $currentUser->getId();
        }

        return false;
    }
}
