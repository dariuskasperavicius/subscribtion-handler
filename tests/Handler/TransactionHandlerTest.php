<?php

namespace App\Tests\Handler;

use App\Handler\TransactionHandler;
use App\Provider\AppStore\AppStoreTransactionFactory;
use App\Provider\AppStore\Security\SecurityAuthenticator;
use App\Repository\SubscriptionRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class TransactionHandlerTest extends TestCase
{
    public function testHandle()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/data/input.json'), true, 512, JSON_THROW_ON_ERROR);

        $repository = $this
            ->getMockBuilder(SubscriptionRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['find', 'save'])
            ->getMock()
        ;
        $repository
            ->expects($this->once())
            ->method('save');

        $handler = new TransactionHandler(
            $repository,
            new NullLogger()
        );

        $transactionFactory = new AppStoreTransactionFactory($this->createMock(SecurityAuthenticator::class));
        $handler->handleTransaction($json, $transactionFactory);
    }
}
