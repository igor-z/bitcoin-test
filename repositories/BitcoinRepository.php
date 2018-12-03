<?php
namespace app\repositories;

use app\components\bitcoinRpc\ClientInterface;
use app\components\bitcoinRpc\CallResponseInterface;
use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;

class BitcoinRepository implements BitcoinRepositoryInterface
{
	protected $bitcoinRpc;
	protected $callbacks = [];

	public function __construct(ClientInterface $bitcoinRpc)
	{
		$this->bitcoinRpc = $bitcoinRpc;
	}

	public function query()
	{
		$responses = $this->bitcoinRpc->send();
		foreach ($responses as $response) {
			$responseId = $response->getId();
			if ($responseId && isset($this->callbacks[$responseId])) {
				call_user_func($this->callbacks[$responseId], $response);
			}
		}
		
		$this->callbacks = [];
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

	public function createAddress(?callable $callback = null) : BitcoinRepositoryInterface
	{
		$this->fetch(['getnewaddress'], function (CallResponseInterface $response) use($callback) {
			if ($callback)
				call_user_func($callback, $response->getResult());
		});

		return $this;
	}

	public function fetchBalance(callable $callback) : BitcoinRepositoryInterface
	{
		$this->fetch(['getbalance'], function (CallResponseInterface $response) use($callback) {
			call_user_func($callback, $response->getResult());
		});

		return $this;
	}

	public function fetchAddresses(callable $callback) : BitcoinRepositoryInterface
	{
		$this->fetch(['listreceivedbyaddress', [0, true]], function (CallResponseInterface $response) use($callback) {
			$addresses = [];
			foreach ($response->getResult() as $address) {
				$addresses[] = $address['address'];
			}

			call_user_func($callback, $addresses);
		});

		return $this;
	}

	public function fetchTransactions(callable $callback) : BitcoinRepositoryInterface
	{
		$this->fetch(['listtransactions'], function (CallResponseInterface $response) use($callback) {
			$transactions = [];
			foreach ($response->getResult() as $transaction) {
				$transactions[] = new TransactionDTO($transaction['txid'], $transaction['address'], $transaction['amount']);
			}

			call_user_func($callback, $transactions);
		});

		return $this;
	}

	protected function fetch(array $call, ?callable $callback = null)
	{
		$id = uniqid();

		$this->bitcoinRpc->addCall($id, $call[0], $call[1] ?? []);
		if ($callback) {
			$this->callbacks[$id] = $callback;
		}
	}
}