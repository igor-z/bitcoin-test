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

    public function query() : array
    {
        $results = [];

        $responses = $this->bitcoinRpc->send();
        foreach ($responses as $response) {
            $responseId = $response->getId();
            if ($responseId && isset($this->callbacks[$responseId])) {
                $results[$responseId] = call_user_func($this->callbacks[$responseId], $response);
            }
        }

        $this->callbacks = [];

        return $results;
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

    public function createAddress(string $resultField) : BitcoinRepositoryInterface
    {
        $this->fetch(['getnewaddress'], $resultField, function (CallResponseInterface $response) {
            return $response->getResult();
        });

        return $this;
    }

    public function fetchBalance(string $resultField) : BitcoinRepositoryInterface
    {
        $this->fetch(['getbalance'], $resultField, function (CallResponseInterface $response) {
            return $response->getResult();
        });

        return $this;
    }

    public function fetchAddresses(string $resultField) : BitcoinRepositoryInterface
    {
        $this->fetch(['listreceivedbyaddress', [0, true]], $resultField, function (CallResponseInterface $response) {
            $addresses = [];
            foreach ($response->getResult() as $address) {
                $addresses[] = $address['address'];
            }

            return $addresses;
        });

        return $this;
    }

    public function fetchTransactions(string $resultField) : BitcoinRepositoryInterface
    {
        $this->fetch(['listtransactions'], $resultField, function (CallResponseInterface $response) {
            $transactions = [];
            foreach ($response->getResult() as $transaction) {
                $transactions[] = new TransactionDTO($transaction['txid'], $transaction['address'], $transaction['amount']);
            }

            return $transactions;
        });

        return $this;
    }

    protected function fetch(array $call, string $resultField, callable $callback)
    {
        $this->bitcoinRpc->addCall($resultField, $call[0], $call[1] ?? []);
        $this->callbacks[$resultField] = $callback;
    }
}