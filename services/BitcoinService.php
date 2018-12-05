<?php
namespace app\services;

use app\DTOs\BalanceDetails;
use app\DTOs\CredentialsDTO;
use app\repositories\BitcoinRepositoryInterface;

class BitcoinService implements BitcoinServiceInterface
{
    protected $repository;

    public function __construct(BitcoinRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getCredentials() : CredentialsDTO
    {
        return $this->repository->getCredentials();
    }

    public function createAddress() : string
    {
        $response = $this->repository
            ->createAddress('address')
            ->query();

        return $response['address'];
    }

    public function getBalanceDetails() : BalanceDetails
    {
        $details = $this->repository
            ->fetchAddresses('addresses')
            ->fetchBalance('balance')
            ->fetchTransactions('transactions')
            ->query();

        return new BalanceDetails($details['addresses'], $details['transactions'], $details['balance']);
    }
}