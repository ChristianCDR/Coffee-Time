<?php

namespace App\Tests\Unit\Service;

use App\Service\ConsumerService;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ConsumerServiceTest extends TestCase
{
    private $cache;
    private $logger;
    private $item;
    private $consumer;

    public function setUp(): void
    {
        $this->cache = $this->createMock(CacheItemPoolInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->item = $this->createMock(ItemInterface::class);
        $this->consumer = 'consumer_name';
    }

    private function getConsumerService(): ConsumerService
    {
        return new ConsumerService (
            $this->cache,
            $this->logger
        );
    }

    public function testRequestStopSuccess(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with('consumer_stop_' . $this->consumer)
            ->willReturn($this->item)
        ;

        $this->item
            ->expects($this->once())
            ->method('set')
            ->with(true)
            ->willReturnSelf()
        ;

        $this->item
            ->expects($this->once())
            ->method('expiresAfter')
            ->with(600)
            ->willReturnSelf()
        ;

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($this->item)
        ;

        $this->getConsumerService()->requestStop($this->consumer);
    }

    public function testRequestStopThrowsException(): void
    {
        $exception = new \RuntimeException('Cache error');

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->willThrowException($exception)
        ;

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception while requesting consumer stop', $this->callback(function($context) use ($exception) {
                return $context['consumer'] === $this->consumer
                    && $context['exception'] === $exception
                ;
            }))
        ;

        $this->getConsumerService()->requestStop($this->consumer);
    }

    public function testshouldStopSuccess(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with('consumer_stop_'. $this->consumer)
            ->willReturn($this->item)
        ;

        $this->item
            ->expects($this->once())
            ->method('isHit')
            ->willReturn(true)
        ;

        $result = $this->getConsumerService()->shouldStop($this->consumer);

        $this->assertTrue($result);
    }

    public function testShouldStopThrowsException(): void
    {
        $exception = new \RuntimeException('Cache error');

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->willThrowException($exception)
        ;

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Unable to ckeck stop flag in cache', $this->callback(function($context) use ($exception) {
                return $context['consumer'] === $this->consumer
                && $context['exception'] === $exception;
            }))
        ;

        $result = $this->getConsumerService()->shouldStop($this->consumer);

        $this->assertFalse($result);
    }

    public function testclearStopFlagSuccess(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('deleteItem')
            ->with('consumer_stop_'. $this->consumer)
            ->willReturn(true)
        ;

        $this->logger->expects($this->never())->method('warning');
        $this->logger->expects($this->never())->method('error');

        $this->getConsumerService()->clearStopFlag($this->consumer);
    }

    public function testClearStopFlagReturnsFalse():void
    {
        $this->cache
            ->expects($this->once())
            ->method('deleteItem')
            ->with('consumer_stop_' . $this->consumer)
            ->willReturn(false)
        ;

        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Failed to delete stop flag from cache',
                ['consumer' => $this->consumer]
            )
        ;

        $this->getConsumerService()->clearStopFlag($this->consumer);
    }

    public function testClearStopFlagThrowsException(): void
    {
        $exception = new \RuntimeException('Cache error');

        $this->cache
            ->expects($this->once())
            ->method('deleteItem')
            ->willThrowException($exception)
        ;

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Exception while deleting stop flag from cache', $this->callback(function($context) use ($exception) {
                return $context['consumer'] === $this->consumer
                    && $context['exception'] === $exception
                ;
            }))
        ;

        $this->getConsumerService()->clearStopFlag($this->consumer);
    }
}