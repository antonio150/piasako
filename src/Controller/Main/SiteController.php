<?php

namespace App\Controller\Main;

use App\Entity\Main\Site;
use App\Form\SiteForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/site')]
final class SiteController extends AbstractController
{
    private function form(
        Request                $request,
        Site                  $site,
        EntityManagerInterface $entityManager,
    ): Response
    {
        /** @var User $currentuser */
        $currentuser = $this->getUser();

        $isView = $request->query->get('view', false);

        $form = $this->createForm(SiteForm::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            
            // Check if designation already exists
            $existingSite = $entityManager->getRepository(Site::class)
                ->findOneBy(['sitRaisonsociale' => $site->getSitRaisonsociale()]);

            if ($existingSite && $existingSite->getId() !== $site->getId()) {
                $form->get('sitRaisonsociale')->addError(
                    new \Symfony\Component\Form\FormError('Cette désignation existe déjà.')
                );
                return $this->render('espace_admin/site/form.html.twig', [
                    'site' => $site,
                    'form' => $form->createView(),
                    'isView' => false,
                ]);
            }


            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('espace_admin/site/form.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
            'isView' => false,
        ]);
    }

    #[Route('/', name: 'app_site_index', methods: ['GET'])]
    public function index(
        Request                $request,
        EntityManagerInterface $entityManager): Response
    {
        $eActif = $request->query->get('__eActif', '1');

        $queryBuilder = $entityManager->getRepository(Site::class)
            ->createQueryBuilder('p');

        // Filter by estActif
        if ($eActif !== '') {
            $queryBuilder->andWhere('p.estActif = :estActif')
                ->setParameter('estActif', (bool)$eActif);
        }

        $queryBuilder->orderBy('p.updatedAt', 'DESC');

        $site = $queryBuilder->getQuery()->getResult();

       
        return $this->render('espace_admin/site/index.html.twig', [
            'sites' => $site,
        ]);
    }

    #[Route('/new', name: 'app_site_new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $site = new Site();
        return $this->form($request, $site, $entityManager);

    }

    #[Route('/show/{id}', name: 'app_site_show', methods: ['GET'])]
    public function show(Site $site): Response
    {
        $form = $this->createForm(SiteForm::class, $site, ['disabled' => true]);

        return $this->render('espace_admin/site/form.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
            'isView' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_site_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        Site                  $site,
        EntityManagerInterface $entityManager
    ): Response
    {
        return $this->form($request, $site, $entityManager);
    }


    #[Route('/{id}/delete', name: 'app_site_delete', methods: ['POST', 'GET'])]
    public function delete(
        Request                $request,
        Site                  $site,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $currentuser */
        $currentuser = $this->getUser();
       
        if ($site->isEstActif()) {
            // if ($this->isDeletable($permi, $entityManager)) {
                $site->setEstActif(false);
            // }
        } else {
            $site->setEstActif(true);
        }

        $entityManager->persist($site);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
