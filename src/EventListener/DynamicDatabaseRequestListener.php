<?php
namespace App\EventListener;

use App\Service\DatabaseSwitcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DynamicDatabaseRequestListener implements EventSubscriberInterface
{
    private DatabaseSwitcher $databaseSwitcher;

    public function __construct(DatabaseSwitcher $databaseSwitcher)
    {
        $this->databaseSwitcher = $databaseSwitcher;
    }

    public static function getSubscribedEvents(): array
    {
        // Priorité élevée pour s'assurer que la DB dynamique est configurée tôt
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 30],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();
        $db = $session->get('dynamic_db');

        if (!$db) {
            return;
        }

        try {
            $this->databaseSwitcher->switchDatabase($db);
        } catch (\Throwable $e) {
            // Échec de la configuration dynamique : ne pas interrompre la requête
            error_log('[DynamicDatabaseRequestListener] switchDatabase failed: ' . $e->getMessage());
        }
    }
}
