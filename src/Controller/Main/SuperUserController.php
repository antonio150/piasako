<?php

namespace App\Controller\Main;

use App\Entity\Main\SuperUser;
use App\Entity\User;
use App\Form\SuperUserForm;
use App\Form\UserForm;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/main/user')]
final class SuperUserController extends AbstractController
{
    #[Route('/', name: 'app_superuser_index', methods: ['GET'])]
    public function index(
        Request     $request,
        UserService $userService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $p_eActif = $request->query->get('__eActif', 1);

        $estActif = !($p_eActif == 0);

        $users = $entityManager->getRepository(SuperUser::class)->findBy([
            'estActif' => $estActif
        ]);

        return $this->render('espace_admin/super_admin/index.html.twig', [
            'users' => $users,
            'estActif' => $estActif
        ]);
    }

    #[Route('/new', name: 'app_superuser_new', methods: ['GET', 'POST'])]
    public function new(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $clientAdminId = $currentUser?->getIdclientAdmin();

        $user = new SuperUser();

        $options = [
            'password' => true,
            'is_edit' => false,
        ];

        $form = $this->createForm(SuperUserForm::class, $user, $options);
        $form->handleRequest($request);
        $date = new \DateTime();
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le champ password et rôle
            $roles = ["ROLE_USER"];
            $plainPassword = $form->get('password')->getData();

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }


            $user->setRoles($roles);
          
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_superuser_index', ['__eActif' => $user->isEstActif()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('espace_admin/super_admin/form.html.twig', [
            'user' => $user,
            'form' => $form,
            'fromProfile' => false,
            'isEdit' => false,
        ]);
    }

    #[Route('/5449695{id}6569596/edit', name: 'app_superuser_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                     $request,
        SuperUser                        $user,
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        /** @var SuperUser $currentUser */
        $currentUser = $this->getUser();

        $isOwnProfile = ((isset($currentUser) && $currentUser->getId()) === $user->getId());

        $isFormProfile = $request->query->get('fromProfile');

        if (( !$isOwnProfile) ||
            (!$isOwnProfile && $isFormProfile)
        ) {
            return $this->redirectToRoute('app_superuser_index');
        }

        $isSuperAdmin = false;
        if (in_array("ROLE_SUPERADMIN", $user->getRoles())) {
            $isSuperAdmin = true;
        }

        $options = [
            'is_edit' => true,
        ];
        $fromProfile = $request->query->get('fromProfile', 0) == 1;

        $form = $this->createForm(SuperUserForm::class, $user, $options);
        $form->handleRequest($request);
        $date = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = ["ROLE_USER"];
            $plainPassword = $form->get('password')->getData();
           

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $user->setRoles($roles);

            if ($isSuperAdmin) {
                $user->setRoles(['ROLE_SUPERADMIN']);
            }

           

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_superuser_index', ['__eActif' => $user->isEstActif()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('espace_admin/super_admin/form.html.twig', [
            'user' => $user,
            'form' => $form,
            'isOwnProfile' => $isOwnProfile,
            'fromProfile' => $fromProfile,
            'isEdit' => true,
        ]);
    }

    #[Route('/espace-admin/user/info', name: 'app_superuser_info', methods: ['GET'])]
    public function userInfo(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        return $this->json([
          
            'site' => $user->getSite() ? [
                'idsite' => $user->getSite()->getIdSite(),
                'nom' => $user->getSite()->getDenomination(),
            ] : null,
        ]);
    }

    #[Route('/5449695{id}6569596/delete', name: 'app_superuser_delete', methods: ['GET'])]
    public function delete(SuperUser $user, EntityManagerInterface $entityManager): Response
    {
        if ($user->isEstActif()) {
            $user->setEstActif(false);
        } else {
            $user->setEstActif(true);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_superuser_index', ['__eActif' => !$user->isEstActif()], Response::HTTP_SEE_OTHER);
    }

    


}
