<?php
namespace app\tests\unit\components\services;

use app\components\bitcoinRpc\CallResponse;
use app\components\bitcoinRpc\Client;
use app\DTOs\CredentialsDTO;
use app\DTOs\TransactionDTO;
use app\repositories\BitcoinRepository;

class BitcoinRepositoryTest extends \Codeception\Test\Unit
{
	public function testBatchFetchProvider()
	{
		$callResponses = [
			'transactions' => new CallResponse([
				'result' => [
					[
						'address' => '36rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN',
						'category' => '',
						'amount' => '0.0005',
						'confirmations' => 0,
						'label' => '',
						'txid' => 'cb00d207b76df7b4907db4a82ad539f0e62ccfa08fea5efe7751678025ffa98a',
					],
					[
						'address' => '72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN',
						'category' => '',
						'amount' => '0.001',
						'confirmations' => 0,
						'label' => '',
						'txid' => 'cb00d207b76df7b4907db4a82ad539f0e62bcfa08fea5efe7751678025ffa98a',
					],
				],
				'error' => null,
				'id' => 'transactions',
			]),
			'balance' => new CallResponse([
				'result' => 100.00000000,
				'error' => null,
				'id' => 'balance',
			]),
			'addresses' => new CallResponse([
				'result' => [
					[
						'address' => '36rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN',
						'account' => '',
						'amount' => '',
						'confirmations' => 0,
						'label' => '',
						'txids' => [],
					],
					[
						'address' => '72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN',
						'account' => '',
						'amount' => '',
						'confirmations' => 0,
						'label' => '',
						'txids' => [],
					],
				],
				'error' => null,
				'id' => 'addresses',
			]),
		];

		$expectedQueryResults = [
			'transactions' => [
				new TransactionDTO('cb00d207b76df7b4907db4a82ad539f0e62ccfa08fea5efe7751678025ffa98a', '36rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN', '0.0005'),
				new TransactionDTO('cb00d207b76df7b4907db4a82ad539f0e62bcfa08fea5efe7751678025ffa98a', '72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN', '0.001'),
			],
			'balance' => 100.00000000,
			'addresses' => ['36rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN', '72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN'],
		];

		return [
			$expectedQueryResults,
			$callResponses,
		];
	}

	public function testGetCredentials()
	{
		$rpcClient = new Client('user', 'password', 'host', 8555);

		$repository = new BitcoinRepository($rpcClient);
		$this->assertEquals(new CredentialsDTO($rpcClient->getHost(), $rpcClient->getUser(), $rpcClient->getPassword(), $rpcClient->getPort()), $repository->getCredentials());
	}

	public function testCreateAddress()
	{
		/** @var Client $testRpcClient */
		$testRpcClient = $this->make('app\components\bitcoinRpc\Client', [
			'send' => function () {
				return [
					new CallResponse([
						'result' => '72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN',
						'id' => 'address',
						'error' => null,
					]),
				];
			}
		]);

		$repository = (new BitcoinRepository($testRpcClient))
			->createAddress('address');

		$callRequests = $testRpcClient->buildRequestData()['json'];

		$this->assertCount(1, $callRequests);

		$callRequest = reset($callRequests);
		$this->assertEquals('getnewaddress', $callRequest['method']);
		$this->assertEquals([], $callRequest['params']);
		$this->assertEquals('address', $callRequest['id']);

		$queryResponse = $repository->query();

		$this->assertEquals('72rXgYwyfVM6mjDhchNjhhNJgAuWvsNoiN', $queryResponse['address']);
	}

	public function testBatchFetch()
	{
		list($expectedQueryResults, $testRpcCallResponses) = $this->testBatchFetchProvider();

		/** @var Client $testRpcClient */
		$testRpcClient = $this->make('app\components\bitcoinRpc\Client', [
			'send' => function () use($testRpcCallResponses) {
				return [
					$testRpcCallResponses['transactions'],
					$testRpcCallResponses['balance'],
					$testRpcCallResponses['addresses'],
				];
			}
		]);

		$repository = (new BitcoinRepository($testRpcClient))
			->fetchTransactions('transactions')
			->fetchBalance('balance')
			->fetchAddresses('addresses');

		$callRequests = $testRpcClient->buildRequestData()['json'];

		$this->assertCount(3, $callRequests);

		$callRequest = reset($callRequests);
		$this->assertEquals('listtransactions', $callRequest['method']);
		$this->assertEquals([], $callRequest['params']);
		$this->assertEquals('transactions', $callRequest['id']);

		$callRequest = next($callRequests);
		$this->assertEquals('getbalance', $callRequest['method']);
		$this->assertEquals([], $callRequest['params']);
		$this->assertEquals('balance', $callRequest['id']);

		$callRequest = next($callRequests);
		$this->assertEquals('listreceivedbyaddress', $callRequest['method']);
		$this->assertEquals([0, true], $callRequest['params']);
		$this->assertEquals('addresses', $callRequest['id']);

		$queryResults = $repository->query();
		foreach ($queryResults as $id => $queryResult) {
			$this->assertEquals($queryResult, $expectedQueryResults[$id]);
		}
	}
}