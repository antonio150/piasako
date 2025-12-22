<?php

namespace App\Controller;

use App\Entity\Dynamic\Utilisateur;
use App\Entity\Main\SuperUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login_main')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        /** @var SuperUser $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/login-dynamic', name: 'app_login_dynamic')]
    public function login_dynamic(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        /** @var Utilisateur $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout_main')]
    public function logout(SessionInterface $session): void
    {
        // Supprimer la session de vérification 2FA
        if ($session->has('isTwoFactorVerified')) {
            $session->remove('isTwoFactorVerified');
        }
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/logout-dynamic', name: 'app_logout_dynamic')]
    public function logout_dynamic(SessionInterface $session): void
    {
        // Supprimer la session de vérification 2FA
        if ($session->has('isTwoFactorVerified')) {
            $session->remove('isTwoFactorVerified');
        }
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/access-denied', name: 'app_access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('errors/error403.html.twig', [],
            new Response('', Response::HTTP_FORBIDDEN)
        );
    }
    
}
