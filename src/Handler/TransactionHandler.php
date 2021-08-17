<?php

namespace App\Handler;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Exception\OperationFailedException;
use App\Provider\AppStore\TransactionFactoryInterface;
use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

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

    public function handleTransaction(array $data, TransactionFactoryInterface $transactionFactory): void
    {
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
            $subscription = new Subscription();
        }

        $transaction->setSubscription($subscription);

        switch ($transaction->getStatus()) {
            case (Transaction::CANCEL):
                $subscription->setAutoRenew(false);
                break;
            case (Transaction::INITIAL):
            case (Transaction::DID_RENEW):
                $subscription->setEndDate($transaction->getEndDate());
                $subscription->setAutoRenew(true);
                $subscription->setActive(true);
                break;
            case (Transaction::DID_FAIL_TO_RENEW):
            default:
                //do nothing
        }
    }
}
