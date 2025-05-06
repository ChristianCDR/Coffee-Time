<?php

namespace App\EventSubscriber;

use App\Service\ConsumerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Psr\Log\LoggerInterface;

class MessengerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ConsumerService $consumerService,
        private LoggerInterface $logger
    ){}

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning'
        ];
    }

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        try {
            if ($this->consumerService->shouldStop('async')) {
                $event->getWorker()->stop();
            }
        }
        catch (\Throwable $e) {
            $this->logger->warning('Erreur de l\'appel de shouldStop(): ' . $e->getMessage());
            throw new \RuntimeException('Could not evaluate stop condition.');
        } 
    }
}