<?php

namespace App\Handler;

use App\Entity\Transaction;
use App\Exception\OperationFailedException;
use App\Provider\AppStore\TransactionFactoryInterface;
use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransactionHandler
{
    use LoggerAwareTrait;

    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        LoggerInterface $logger,
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
    }

    public function handleTransaction(Request $request, TransactionFactoryInterface $transactionFactory): void
    {
        $data = $this->getJson($request);

        try {
            $transaction = $transactionFactory->createTransaction($data);
            $this->updateSubscription($transaction);
        } catch (OperationFailedException $exception) {
            $this->logger->critical($exception->getMessage());
            return;
        }

        $this->subscriptionRepository->save($transaction->getSubscription());
    }

    private function updateSubscription(Transaction $transaction): void
    {
        $subscription = $this->subscriptionRepository->find(
            ['productId' => $transaction->getProductId()]
        );

        if ($subscription === null) {
            throw new OperationFailedException('Subscription was not found');
        }

        $transaction->setSubscription($subscription);

        switch ($transaction->getStatus()) {
            case (Transaction::CANCEL):
                $subscription->setAutoRenew(false);
                break;
            case (Transaction::INITIAL):
            case (Transaction::DID_RENEW):
                $subscription->setEndDate(
                    $transaction->getEndDate()
                );
                $subscription->setAutoRenew(true);
                $subscription->setActive(true);
                break;
            case (Transaction::DID_FAIL_TO_RENEW):
            default:
                //do nothing
        }
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
