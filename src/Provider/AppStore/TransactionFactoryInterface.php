<?php

namespace App\Provider\AppStore;

use App\Entity\Transaction;
use App\Exception\OperationFailedException;
use App\Provider\AuthenticatorInterface;

interface TransactionFactoryInterface
{
    /**
     * @param array $data
     * @return Transaction
     *
     * @throws OperationFailedException
     */
    public function createTransaction(array $data): Transaction;

    public function getAuthenticator(): AuthenticatorInterface;

    public function getName(): string;
}
