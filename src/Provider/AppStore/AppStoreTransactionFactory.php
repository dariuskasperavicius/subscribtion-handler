<?php

namespace App\Provider\AppStore;

use App\Entity\Transaction;
use App\Exception\OperationFailedException;
use App\Provider\AppStore\Security\SecurityAuthenticator;
use App\Provider\AuthenticatorInterface;
use Carbon\Carbon;

class AppStoreTransactionFactory implements TransactionFactoryInterface
{
    private SecurityAuthenticator $authenticator;

    public function __construct(
        SecurityAuthenticator $authenticator
    ) {
        $this->authenticator = $authenticator;
    }

    public function getName(): string
    {
        return __CLASS__;
    }

    /**
     * @inheritDoc
     */
    public function createTransaction(array $data): Transaction
    {
        $this->validate($data);

        $transaction = new Transaction();
        $transaction->setTransactionId($data['unified_receipt']['latest_receipt_info'][0]['transaction_id']);
        $transaction->setProductId($data['auto_renew_product_id']);

        $endDate = $data['unified_receipt']['latest_receipt_info']['expires_date'] ?? null;
        if ($endDate !== null) {
            $transaction->setEndDate(new Carbon($endDate));
        }
        $transaction->setStatus($this->mapStatus($data['notification_type']));
        return $transaction;
    }

    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    /**
     * @param array $data
     * @throws OperationFailedException
     */
    private function validate(array $data): void
    {
        if (
            !isset($data['notification_type'])
            && $this->compareEnv($data['environment'], $_ENV['APP_ENV']) !== true
        ) {
            throw new OperationFailedException('Not valid transaction');
        }
    }

    private function compareEnv($clientEnv, $machineEnv): bool
    {
        //simple strtolower may fail
        $envMap = [
            'PROD' => 'prod'
        ];

        return isset($envMap[$clientEnv]) && $envMap[$clientEnv] === strtolower($machineEnv);
    }

    private function mapStatus(string $status)
    {
        return  match ($status) {
            'DID_RENEW' => Transaction::DID_RENEW,
            'DID_FAIL_TO_RENEW' => Transaction::DID_FAIL_TO_RENEW,
            'CANCEL' => Transaction::CANCEL,
            default => Transaction::INITIAL
        };
    }
}
