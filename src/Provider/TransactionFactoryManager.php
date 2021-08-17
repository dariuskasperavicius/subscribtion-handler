<?php

namespace App\Provider;

use App\Exception\OperationFailedException;
use App\Provider\AppStore\TransactionFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class TransactionFactoryManager
{
    /**
     * @var TransactionFactoryInterface[]
     */
    private array $factories;

    public function addProvider(TransactionFactoryInterface $provider)
    {
        $this->factories[$provider->getName()] = $provider;
    }

    /**
     * @param string $name
     * @param Request $request
     * @return TransactionFactoryInterface
     *
     * @throws OperationFailedException
     */
    public function getProvider(string $name, Request $request): TransactionFactoryInterface
    {
        $provider = $this->factories[$name] ?? null;
        if ($provider !== null && $provider->getAuthenticator()->isValid($request)) {
            return $provider;
        }

        throw new OperationFailedException('Could not crete valid provider');
    }
}
