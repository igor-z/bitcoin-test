<?php
namespace app\repositories;

use app\components\bitcoinRpc\ClientInterface;
use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;
use Denpa\Bitcoin\Responses\BitcoindResponse;

class BitcoinRepository implements BitcoinRepositoryInterface
{
	protected $bitcoinRpc;

	public function __construct(ClientInterface $bitcoinRpc)
	{
		$this->bitcoinRpc = $bitcoinRpc;
	}

	public function getCredentials() : CredentialsDTO
	{
		return new CredentialsDTO(
			$this->bitcoinRpc->getHost(),
			$this->bitcoinRpc->getUser(),
			$this->bitcoinRpc->getPassword(),
			$this->bitcoinRpc->getPort()
		);
	}

	public function createAddress() : string
	{
		$this->bitcoinRpc->send();

		return $this;
	}

	public function fetchBalance() : float
	{
		$this->bitcoinRpc->query('fetchBalance', 'getbalance');

		return $this->bitcoinRpc->getBalance()->get();
	}

	/**
	 * @return string[]
	 */
	public function fetchAddresses() : array
	{
		/** @var BitcoindResponse $response */
		$response = $this->bitcoinRpc->listReceivedByAddress(0, true);

		$addresses = [];
		foreach ($response->get() as $address) {
			$addresses[] = $address['address'];
		}

		return $addresses;
	}

	/**
	 * @var string $address
	 * @return TransactionDTO[]
	 */
	public function fetchTransactions() : array
	{
		/** @var BitcoindResponse $response */
		$response = $this->bitcoinRpc->listTransactions();

		$transactions = [];
		foreach ($response->get() as $transaction) {
			$transactions[] = new TransactionDTO($transaction['txid'], $transaction['address'], $transaction['amount']);
		}

		return $transactions;
	}
}