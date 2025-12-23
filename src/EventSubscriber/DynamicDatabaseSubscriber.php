<?php 
namespace App\EventSubscriber;

use App\Service\DatabaseSwitcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DynamicDatabaseSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private DatabaseSwitcher $databaseSwitcher
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        $db = $request->getSession()->get('dynamic_database');

        if ($db) {
            $this->databaseSwitcher->switchDatabase($db);
        }
    }
}
