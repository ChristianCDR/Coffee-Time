<?php

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class ConsumerService
{

    public function __construct(
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger
    ) {}

    public function requestStop(string $consumer): void
    {
        $this->clearStopFlag($consumer);

        try {
            $item = $this->cache->getItem('consumer_stop_'. $consumer);
            $item->set(true);
            $item->expiresAfter(600);

            $this->cache->save($item);
        }
        catch (\Throwable $e) {
            $this->logger->error('Exception while requesting consumer stop', [
                'consumer' => $consumer,
                'exception' => $e
            ]);
        }   
    }

    public function shouldStop(string $consumer): bool
    {
        try {
            $item = $this->cache->getItem('consumer_stop_'. $consumer);
            return $item->isHit();
        }
        catch (\Throwable $e) {
            $this->logger->error('Unable to ckeck stop flag in cache', [
                'consumer' => $consumer,
                'exception' => $e
            ]);
        }

        return false;
    }

    public function clearStopFlag(string $consumer): void
    {
        try {
            $success = $this->cache->deleteItem('consumer_stop_'. $consumer);

            if (!$success) {
                $this->logger->warning('Failed to delete stop flag from cache', ['consumer' => $consumer]);
            }
        }
        catch(\Throwable $e) {
            $this->logger->error('Exception while deleting stop flag from cache', [
                'consumer' => $consumer,
                'exception' => $e
            ]);
        }
    }
}