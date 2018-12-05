<?php
namespace app\repositories;

use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;

interface BitcoinRepositoryInterface
{
    public function getCredentials() : CredentialsDTO;

    public function query() : array;

    public function createAddress(string $resultField) : BitcoinRepositoryInterface;

    public function fetchBalance(string $resultField) : BitcoinRepositoryInterface;

    /**
     * @return string[]
     */
    public function fetchAddresses(string $resultField) : BitcoinRepositoryInterface;

    /**
     * @var string $address
     * @return TransactionDTO[]
     */
    public function fetchTransactions(string $resultField) : BitcoinRepositoryInterface;
}