<?php
namespace app\repositories;

use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;

interface BitcoinRepositoryInterface
{
	public function getCredentials() : CredentialsDTO;

	public function query();

	public function createAddress(?callable $callback = null) : BitcoinRepositoryInterface;

	public function fetchBalance(callable $callback) : BitcoinRepositoryInterface;

	/**
	 * @return string[]
	 */
	public function fetchAddresses(callable $callback) : BitcoinRepositoryInterface;

	/**
	 * @var string $address
	 * @return TransactionDTO[]
	 */
	public function fetchTransactions(callable $callback) : BitcoinRepositoryInterface;
}