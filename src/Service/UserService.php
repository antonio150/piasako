<?php

namespace App\Service;

use App\Entity\Main\SuperUser;
use App\Entity\User;
use App\Entity\Visite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserService
{

    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getUsersByCritera(Bool $estActif): array
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        if (!$currentUser) return [];

        $qb = $this->entityManager->getRepository(SuperUser::class)->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        $qb->where('u.estActif = :estActif')
            ->setParameter('estActif', $estActif);

        return $qb->getQuery()->getResult();
    }

    public function getUsersBySuperAdmin(Bool $estActif): array
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->security->getUser();

        if (!$currentUser) return [];

        $qb = $this->entityManager->getRepository(SuperUser::class)->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        $qb->where('u.estActif = :estActif')
            ->setParameter('estActif', $estActif);

       

        return $qb->getQuery()->getResult();
    }
}
