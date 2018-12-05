<?php
namespace app\DTOs;

class TransactionDTO
{
    private $id;
    private $address;
    private $amount;

    public function __construct(string $id, string $address, float $amount)
    {
        $this->id = $id;
        $this->address = $address;
        $this->amount = $amount;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}