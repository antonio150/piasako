<?php

namespace App\Controller\Dynamic;

use App\Entity\Main\SuperUser;
use App\Service\DataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeDynamicController extends AbstractController
{
    #[Route('/espaceclient', name: 'app_home_dynamic')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(
        EntityManagerInterface $entityManager
       
    ): Response
    {
        /** @var SuperUser $currentUser */
        $currentUser = $this->getUser();
        return $this->redirectToRoute('app_tache_index');
    }
}
