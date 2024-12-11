<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est inactif ! Impossible de se connecter'
            );
        }
        if ($user->isBlocked()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est bloqué ! Impossible de se connecter'
            );
        }

        if ($user->isNouveauMdp()) {
            throw new CustomUserMessageAuthenticationException(
                'Vous devez renouveller votre mot de passe pour vous connecter !'
            );
        }
    }
    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkPreAuth($user);
    }
}

