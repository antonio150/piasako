<?php 

namespace App\Controller\Dynamic;

use App\Entity\Dynamic\Taches;
use App\Form\TachesForm;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use App\Service\RoleCheckerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/espaceclient/tache')]
final class TachesController extends AbstractController
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

    private function switchToDatabase(): void
    {
        $this->databaseSwitcher->switchDatabase();
    }

    private function form(
        Request $request, Taches $taches, $entityManager
    )
    {
        $isView = $request->query->get('view', false);

        $form = $this->createForm(TachesForm::class, $taches, [
            'disabled' => $isView,
        ]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taches);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('espace_client/tache/form.html.twig', [
            'taches' => $taches,
            'form' => $form->createView(),
            'isView' => false,
        ]);
    }

    #[Route('/', name: 'app_tache_index', methods: ['POST', 'GET'])]
    public function index(Request $request): Response
    {
        // Vérifier les rôles via le service
        $requiredRoles = ['ROLE_SUPERVISEUR', 'ROLE_ADMIN'];
        $roleCheck = $this->roleCheckerService->checkUserRolesWithResponse($request, $requiredRoles);
        // Si le résultat est une JsonResponse, retourner l'erreur
        if ($roleCheck instanceof JsonResponse) {
            return $roleCheck;
        }
        $this->switchToDatabase();
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();

        $p_eActif = $request->query->get('__eActif', 1);
        $estActif = !($p_eActif == 0);
      
        $taches = $entityManager->getRepository(Taches::class)->findBy([
            'estActif' => $estActif
        ]);

        return $this->render('espace_client/tache/index.html.twig', [
            'taches' => $taches,
            'estActif' => $estActif
        ]);
    }

    #[Route("/new", name:"app_tache_new", methods:["POST", "GET"])]
    public function new(Request $request): Response
    {
        $this->switchToDatabase();
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();
       
       
        $taches = new Taches();

        return $this->form($request, $taches, $entityManager);
    }
}