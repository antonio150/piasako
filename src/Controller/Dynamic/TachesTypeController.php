<?php 

namespace App\Controller\Dynamic;

use App\Entity\Dynamic\Taches;
use App\Entity\Dynamic\TachesType;
use App\Form\TachesForm;
use App\Form\TachesTypeForm;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use App\Service\RoleCheckerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/espaceclient/tachetype')]
final class TachesTypeController extends AbstractController
{
    private DatabaseSwitcher $databaseSwitcher;
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;
    private RoleCheckerService $roleCheckerService;
    public function __construct(
        DatabaseSwitcher $databaseSwitcher,
        RoleCheckerService $roleCheckerService,
        DynamicEntityManagerProvider $dynamicEntityManagerProvider,
    )
    {
        $this->databaseSwitcher = $databaseSwitcher;
        $this->roleCheckerService = $roleCheckerService;
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

    private function form(
        Request $request, TachesType $tachestype, $entityManager, $methode
    )
    {
        $isView = $request->query->get('view', false);

        $form = $this->createForm(TachesTypeForm::class, $tachestype, [
            'disabled' => $isView,
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            if ($methode === "add") {
                $entityManager->persist($tachestype);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_tachetype_index');
        }

        return $this->render('espace_client/tacheType/form.html.twig', [
            'taches' => $tachestype,
            'form' => $form->createView(),
            'isView' => false,
        ]);
    }

    #[Route('/', name: 'app_tachetype_index', methods: ['POST', 'GET'])]
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

        $tachetypes = $entityManager->getRepository(TachesType::class)->findAll();

        return $this->render('espace_client/tacheType/index.html.twig', [
            'tachetypes' => $tachetypes,
        ]);
    }

    #[Route("/new", name:"app_tachetype_new", methods:["POST", "GET"])]
    public function new(Request $request): Response
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
        $tachestype = new TachesType();
        $methode="add";
        return $this->form($request, $tachestype, $entityManager, $methode);
    }

    #[Route("/edit/{id}", name:"app_tachetype_edit", methods:["POST", "GET"])]
    public function edit(Request $request, TachesType $tachestype): Response
    {
        $requiredRoles = ['ROLE_SUPERVISEUR','ROLE_AGENT', 'ROLE_ADMIN'];
        $roleCheck = $this->roleCheckerService->checkUserRolesWithResponse($request, $requiredRoles);

        if ($roleCheck instanceof JsonResponse) {
            return $roleCheck;
        }

        $databasename = $roleCheck['database_name'];
        $this->databaseSwitcher->switchDatabase($databasename);
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();

        // 🔥 recharger l'entité dans le bon EntityManager
        $tachestype = $entityManager
            ->getRepository(TachesType::class)
            ->find($tachestype->getId());
        $methode="edit";
        return $this->form($request, $tachestype, $entityManager,$methode);
    }

}