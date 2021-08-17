<?php

namespace App\Controller;

use App\Exception\OperationFailedException;
use App\Handler\TransactionHandler;
use App\Provider\AppStore\AppStoreTransactionFactory;
use App\Provider\TransactionFactoryManager;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    use LoggerAwareTrait;

    private TransactionFactoryManager $transactionFactoryManager;
    private TransactionHandler $transactionHandler;

    public function __construct(
        TransactionFactoryManager $transactionFactoryManager,
        TransactionHandler $transactionHandler,
        LoggerInterface $logger
    ) {
        $this->transactionFactoryManager = $transactionFactoryManager;
        $this->transactionHandler = $transactionHandler;
        $this->logger = $logger;
    }
    /**
     * @Route("/transaction/app_store", name="transaction", methods={"POST"})
     */
    public function appStoreTransaction(Request $request): JsonResponse
    {
        try {
            $provider = $this->transactionFactoryManager->getProvider(AppStoreTransactionFactory::class, $request);
        } catch (OperationFailedException $exception) {
            $this->logger->critical($exception->getMessage());
            return new JsonResponse('Failed');
        }

        $this->transactionHandler->handleTransaction($request, $provider);
        return new JsonResponse("Success");
    }
}
