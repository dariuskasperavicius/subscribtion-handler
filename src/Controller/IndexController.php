<?php

namespace App\Controller;

use App\Exception\OperationFailedException;
use App\Handler\TransactionHandler;
use App\Provider\AppStore\AppStoreTransactionFactory;
use App\Provider\TransactionFactoryManager;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $data = $this->getJson($request);
        try {
            $provider = $this->transactionFactoryManager->getProvider(AppStoreTransactionFactory::class, $request);
        } catch (OperationFailedException $exception) {
            $this->logger->critical($exception->getMessage());
            return new JsonResponse('Failed');
        }

        $this->transactionHandler->handleTransaction($data, $provider);
        return new JsonResponse("Success");
    }


    /**
     * @param Request $request
     * @return array
     *
     * @throws HttpException
     */
    private function getJson(Request $request): array
    {
        try {
            $content = $request->getContent();
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Invalid json', $exception);
        }
        return $data;
    }
}
