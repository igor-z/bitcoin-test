<?php
/**
 */

namespace app\DTOs;


class BalanceDetails
{
    private $addresses;
    private $transactions;
    private $balance;

    /**
     * BalanceDetails constructor.
     * @param string[] $addresses
     * @param TransactionDTO[] $transactions
     * @param float $balance
     */
    public function __construct(array $addresses, array $transactions, float $balance)
    {
        $this->addresses = $addresses;
        $this->transactions = $transactions;
        $this->balance = $balance;
    }

    public function getBalance() : float
    {
        return $this->balance;
    }

    /**
     * @return TransactionDTO[]
     */
    public function getTransactions() : array
    {
        return $this->transactions;
    }

    /**
     * @return string[]
     */
    public function getAddresses() : array
    {
        return $this->addresses;
    }
}