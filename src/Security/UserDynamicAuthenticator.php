<?php
namespace App\Security;

use App\Entity\Dynamic\Utilisateur;
use App\Service\DatabaseSwitcher;
use App\Service\DynamicEntityManagerProvider;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Serializer\SerializerInterface;

class UserDynamicAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        private DatabaseSwitcher $databaseSwitcher,
        private EntityManagerInterface $mainEntityManager,
        private DynamicEntityManagerProvider $dynamicEntityManagerProvider,
    ) {
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

   
    public function supports(Request $request): bool
    {
        $matches = $request->getPathInfo() === '/login-dynamic' && $request->isMethod('POST');
        error_log('[UserDynamicAuthenticator] supports=' . ($matches ? 'true' : 'false') . ' path=' . $request->getPathInfo());
        return $matches;
    }

    public function authenticate(Request $request): Passport
    {
        error_log('[UserDynamicAuthenticator] authenticate called. IP=' . $request->getClientIp());
        $this->databaseSwitcher->switchDatabase('tapos');
        // Stocke le nom de la base dynamique en session pour pouvoir
        // reconfigurer la connexion sur les prochaines requêtes.
        try {
            if ($request->hasSession()) {
                $request->getSession()->set('dynamic_db', 'tapos');
            }
        } catch (\Throwable $e) {
            // Ne doit pas casser l'authentification si la session n'est pas disponible
            dump('[UserDynamicAuthenticator] unable to store dynamic_db in session: ' . $e->getMessage());
        }
        $entityManager = $this->dynamicEntityManagerProvider->getEntityManager();
        // Récupérer les informations d'authentification depuis la requête.
        // Le formulaire du site envoie `application/x-www-form-urlencoded` (pas JSON),
        // donc on doit d'abord tenter de parser JSON, puis tomber en fallback sur
        // $request->request (données POST classiques).
        $data = null;
        $contentType = $request->headers->get('Content-Type') ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($request->getContent(), true);
        }

        if (!is_array($data) || empty($data)) {
            // fallback pour form-data / x-www-form-urlencoded
            $data = $request->request->all();
        }

        $username = $data['_username'] ?? $request->request->get('_username');
        $password = $data['_password'] ?? $request->request->get('_password');
        $codeVoucher = $data['sitCode'] ?? $request->request->get('sitCode');

        error_log(sprintf('[UserDynamicAuthenticator] credentials: username=%s sitCode=%s', $username ?? 'NULL', $codeVoucher ?? 'NULL'));

        if (!$username || !$password) {
            throw new AuthenticationException('Les paramètres d\'authentification sont manquants.');
        }

        $entityManager->getRepository(Utilisateur::class)->findOneBy(['userLogin' => $username]);

        $site = $this->mainEntityManager
            ->getRepository(\App\Entity\Main\Site::class)
            ->findOneBy(['sitCode' => $codeVoucher]);

        return new Passport(
            new UserBadge($username, function ($userIdentifier) use ($entityManager) {
                return $entityManager->getRepository(Utilisateur::class)->findOneBy(['userLogin' => $userIdentifier]);
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    { 
        $request->getSession()->set('dynamic_database', 'tapos');
        error_log('[UserDynamicAuthenticator] authentication success for user=' . ($token->getUser() ? (is_object($token->getUser()) ? get_class($token->getUser()) : (string)$token->getUser()) : 'NULL') . ' firewall=' . $firewallName);
        // Redirect to espaceclient root to ensure session is used on next request
        return new \Symfony\Component\HttpFoundation\RedirectResponse('/espaceclient/tache');

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        error_log('[UserDynamicAuthenticator] authentication failure: ' . $exception->getMessage());
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new Response(json_encode($data), Response::HTTP_UNAUTHORIZED, ['Content-Type' => 'application/json']);
    }
}