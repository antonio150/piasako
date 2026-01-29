<?php

namespace App\Controller\Dynamic;

use App\Entity\Dynamic\Personne;
use App\Form\PersonneForm;
use App\Form\TachesForm;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use App\Service\RoleCheckerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UploadImageBundle\Service\FileUploader;

#[Route('/espaceclient/personne')]
final class PersonneController extends AbstractController
{
    private DatabaseSwitcher $databaseSwitcher;
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;
    private RoleCheckerService $roleCheckerService;
   
    public function __construct(
        DatabaseSwitcher $databaseSwitcher,
        DynamicEntityManagerProvider $dynamicEntityManagerProvider,
        RoleCheckerService $roleCheckerService,
    )
    {
        $this->databaseSwitcher = $databaseSwitcher;
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
        $this->roleCheckerService = $roleCheckerService;
    }

    private function form(
        Request $request, Personne $personne, $entityManager, FileUploader $fileUploader, $methode
    )
    {
        $isView = $request->query->get('view', false);

        $form = $this->createForm(PersonneForm::class, $personne, [
            'disabled' => $isView,
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photoFile')->getData();

            if ($file) {
                // Appel de TON bundle existant
                $result = $fileUploader->upload($file);
                $personne->setPersPhotoRelative($result['public_path']); 
                $personne->setPersPhotoAbsolute($result['absolute_path']); 
            }
            if($methode === "add"){
                $entityManager->persist($personne);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_personne_index');
        }

        return $this->render('espace_client/personne/form.html.twig', [
            'personne' => $personne,
            'form' => $form->createView(),
            'isView' => $isView,
        ]);
    }

    #[Route('/', name: 'app_personne_index', methods: ['POST', 'GET'])]
    public function index(Request $request): Response
    {
        // Vérifier les rôles via le service
        $requiredRoles = ['ROLE_SUPERVISEUR','ROLE_AGENT', 'ROLE_ADMIN'];
        $roleCheck = $this->roleCheckerService->checkUserRolesWithResponse($request, $requiredRoles);
        // Si le résultat est une JsonResponse, retourner l'erreur
        if ($roleCheck instanceof JsonResponse) {
            return $roleCheck;
        }
        $databasename = $roleCheck['database_name'];
        $this->databaseSwitcher->switchDatabase($databasename);
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();

        $p_eActif = $request->query->get('__eActif', 1);
        $estActif = !($p_eActif == 0);
      
        $personnes = $entityManager->getRepository(Personne::class)->findBy([
            'estActif' => $estActif
        ]);

        return $this->render('espace_client/personne/index.html.twig', [
            'personnes' => $personnes,
            'estActif' => $estActif
        ]);
    }

    #[Route('/new', name:"app_personne_new", methods:["POST", "GET"])]
    public function new(Request $request, FileUploader $fileUploader):Response 
    {
        // Vérifier les rôles via le service
        $requiredRoles = ['ROLE_SUPERVISEUR','ROLE_AGENT', 'ROLE_ADMIN'];
        $roleCheck = $this->roleCheckerService->checkUserRolesWithResponse($request, $requiredRoles);
        // Si le résultat est une JsonResponse, retourner l'erreur
        if ($roleCheck instanceof JsonResponse) {
            return $roleCheck;
        }
        $databasename = $roleCheck['database_name'];
        $this->databaseSwitcher->switchDatabase($databasename);
        $entityManagers = $this->dynamicEntityManagerProvider->getEntityManager();
        $personne = new Personne();
        $methode="add";
        return $this->form($request, $personne, $entityManagers, $fileUploader,$methode);
    }

    #[Route('/edit/{id}', name:"app_personne_edit", methods:["POST", "GET"])]
    public function edit(Request $request,Personne $personne, FileUploader $fileUploader):Response 
    {
        // Vérifier les rôles via le service
        $requiredRoles = ['ROLE_SUPERVISEUR','ROLE_AGENT', 'ROLE_ADMIN'];
        $roleCheck = $this->roleCheckerService->checkUserRolesWithResponse($request, $requiredRoles);
        // Si le résultat est une JsonResponse, retourner l'erreur
        if ($roleCheck instanceof JsonResponse) {
            return $roleCheck;
        }
        $databasename = $roleCheck['database_name'];
        $this->databaseSwitcher->switchDatabase($databasename);
        $entityManagers = $this->dynamicEntityManagerProvider->getEntityManager();
        $personne = $entityManagers
            ->getRepository(Personne::class)
            ->find($personne->getId());
        $methode="edit";
      
        return $this->form($request, $personne, $entityManagers, $fileUploader,$methode);
    }


}