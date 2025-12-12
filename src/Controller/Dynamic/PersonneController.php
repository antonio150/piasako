<?php

namespace App\Controller\Dynamic;

use App\Entity\Dynamic\Personne;
use App\Form\TachesForm;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PersonneController extends AbstractController
{
    private DatabaseSwitcher $databaseSwitcher;
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;
    
   
    public function __construct(
        DatabaseSwitcher $databaseSwitcher,
        DynamicEntityManagerProvider $dynamicEntityManagerProvider,
    )
    {
        $this->databaseSwitcher = $databaseSwitcher;
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

     private function switchToDatabase(): void
    {
        $this->databaseSwitcher->switchDatabase();
    }

    private function form(
        Request $request, Personne $personne, $entityManager
    )
    {
        $isView = $request->query->get('view', false);

        $form = $this->createForm(TachesForm::class, $personne, [
            'disabled' => $isView,
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($personne);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('espace_client/tache/form.html.twig', [
            'taches' => $personne,
            'form' => $form->createView(),
            'isView' => $isView,
        ]);
    }

    #[Route('/', name: 'app_personne_index', methods: ['POST', 'GET'])]
    public function index(Request $request): Response
    {
        $this->switchToDatabase();
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();

        $p_eActif = $request->query->get('__eActif', 1);
        $estActif = !($p_eActif == 0);
      
        $taches = $entityManager->getRepository(Personne::class)->findBy([
            'estActif' => $estActif
        ]);

        return $this->render('espace_client/tache/index.html.twig', [
            'taches' => $taches,
            'estActif' => $estActif
        ]);
    }
}