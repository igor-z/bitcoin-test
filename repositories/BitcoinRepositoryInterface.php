<?php
namespace app\repositories;

use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;

interface BitcoinRepositoryInterface
{
	public function getCredentials() : CredentialsDTO;

	public function createAddress() : string;

	public function fetchBalance() : float;

	/**
	 * @return string[]
	 */
	public function fetchAddresses() : array;

	/**
	 * @var string $address
	 * @return TransactionDTO[]
	 */
	public function fetchTransactions() : array;
}