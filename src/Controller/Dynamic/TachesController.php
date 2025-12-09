<?php 

namespace App\Controller\Dynamic;

use App\Entity\Dynamic\Taches;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tache')]
final class TachesController extends AbstractController
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

    #[Route('/', name: 'app_tache_index', methods: ['POST', 'GET'])]
    public function index(
        Request     $request
    ): Response
    {

        $p_eActif = $request->query->get('__eActif', 1);

        $estActif = !($p_eActif == 0);
        $database_name = "tapos";
        $this->databaseSwitcher->switchDatabase($database_name);
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();
      
        $taches = $entityManager->getRepository(Taches::class)->findBy([
            'estActif' => $estActif
        ]);

        // Logique pour afficher la liste des tâches
        return $this->render('espace_client/tache/index.html.twig', [
            'taches' => $taches,
            'estActif' => $estActif
        ]);
    }
}