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
		$address = null;

		$this->repository
			->createAddress(function ($createdAddress) use(&$address) {
				$address = $createdAddress;
			})
			->query();

		return $address;
	}

	public function getBalanceDetails() : BalanceDetails
	{
		$details = [];

		$this->repository
			->fetchAddresses(function ($addresses) use(&$details) {
				$details['addresses'] = $addresses;
			})
			->fetchBalance(function ($balance) use(&$details) {
				$details['balance'] = $balance;
			})
			->fetchTransactions(function ($transactions) use(&$details) {
				$details['transactions'] = $transactions;
			})
			->query();

		return new BalanceDetails($details['addresses'], $details['transactions'], $details['balance']);
	}
}