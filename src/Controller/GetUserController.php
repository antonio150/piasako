<?php

namespace App\Controller;

use App\Entity\Main\SuperUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetUserController extends AbstractController
{
    #[Route('/api/userBymail/{email}', name: 'app_get_user', methods:["GET"])]
    public function index(EntityManagerInterface $entityManager, $email): JsonResponse
    {
        $user = $entityManager->getRepository(SuperUser::class)->findOneBy([
            'email' => $email
        ]);
        return $this->json($user);
    }
}
