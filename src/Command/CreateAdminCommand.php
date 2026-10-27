<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Creates an admin account, or resets its password')]
final class CreateAdminCommand
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
    ) {
    }

    public function __invoke(SymfonyStyle $io, #[Argument('Email of the admin')] string $email): int
    {
        $password = $io->askHidden('Mot de passe (12 caractères minimum)', static function (?string $value): string {
            if (null === $value || mb_strlen($value) < 12) {
                throw new \RuntimeException('Le mot de passe doit contenir au moins 12 caractères.');
            }

            return $value;
        });

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]) ?? new User($email);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Compte admin "%s" prêt. Connexion sur /login.', $email));

        return Command::SUCCESS;
    }
}
