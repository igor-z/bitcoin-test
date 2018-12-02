<?php
namespace app\services;

use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;
use app\repositories\BitcoinRepositoryInterface;

class BitcoinService
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
		return $this->repository->createAddress();
	}

	public function getBalanceInfo()
	{
		$info = [];

		$this->repository

		return $info;
	}

	public function getBalance() : float
	{
		return $this->repository->fetchBalance();
	}

	/**
	 * @return string[]
	 */
	public function getAddresses() : array
	{
		return $this->repository->fetchAddresses();
	}

	/**
	 * @var string $address
	 * @return TransactionDTO[]
	 */
	public function getTransactions() : array
	{
		return $this->repository->fetchTransactions();
	}
}