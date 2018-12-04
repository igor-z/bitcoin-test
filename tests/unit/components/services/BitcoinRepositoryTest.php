<?php
namespace app\tests\unit\components\services;

use app\components\bitcoinRpc\Client;
use app\repositories\BitcoinRepository;
use Codeception\Stub\Expected;

class BitcoinRepositoryTest extends \Codeception\Test\Unit
{
	public function testFetch()
	{
		$rpcClient = new Client('user', 'password', 'host', 8555);

		$transactionsRequestId = null;
		/** @var Client $testRpcClient */
		$testRpcClient = $this->make($rpcClient, [
			'send' => function () use(&$transactionsRequestId) {
				$transactionsRequestId = 1;
			}
		]);

		$repository = new BitcoinRepository($testRpcClient);

		$repository
			->fetchTransactions(function ($transactions) {

			})
			->fetchBalance(function ($balance) {

			})
			->fetchAddresses(function ($addresses) {

			});

		$callRequests = $testRpcClient->buildRequestData()['json'];

		$this->assertCount(3, $callRequests);

		$callRequest = reset($callRequests);

		$this->assertEquals('listtransactions', $callRequest['method']);
		$this->assertEquals([], $callRequest['params']);
		$this->assertArrayHasKey('id', $callRequest);
		$transactionsRequestId = $callRequest['id'];

		$callRequest = next($callRequests);

		$this->assertEquals('getbalance', $callRequest['method']);
		$this->assertEquals([], $callRequest['params']);
		$this->assertArrayHasKey('id', $callRequest);
		$balanceRequestId = $callRequest['id'];

		$callRequest = next($callRequests);

		$this->assertEquals('listreceivedbyaddress', $callRequest['method']);
		$this->assertEquals([0, true], $callRequest['params']);
		$this->assertArrayHasKey('id', $callRequest);
		$addressesRequestId = $callRequest['id'];

		$repository->query();
	}
}