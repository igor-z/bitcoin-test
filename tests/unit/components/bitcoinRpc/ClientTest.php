<?php
namespace tests\components\bitcoinRpc;

use app\components\bitcoinRpc\BadRemoteCallException;
use app\components\bitcoinRpc\CallResponse;
use app\components\bitcoinRpc\Client as RpcClient;

class ClientTest extends \Codeception\Test\Unit
{
	public function testRequest()
	{
		$client = new RpcClient('user', 'password', 'host', 8555);

		$this->assertEquals('user', $client->getUser());
		$this->assertEquals('password', $client->getPassword());
		$this->assertEquals('host', $client->getHost());
		$this->assertEquals(8555, $client->getPort());

		$client->addCall('first', 'getbalance');
		$client->addCall('second', 'listreceivedbyaddress', [0, true]);

		$this->assertEquals([
			'json' => [
				[
					'jsonrpc' => '2.0',
					'id' => 'first',
					'method' => 'getbalance',
					'params' => [],
				],
				[
					'jsonrpc' => '2.0',
					'id' => 'second',
					'method' => 'listreceivedbyaddress',
					'params' => [0, true],
				],
			],
		], $client->buildRequestData());

		$testResponse = [
			[
				'result' => 100.00000000,
				'error' => null,
				'id' => 'first',
			],
			[
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
				'id' => 'second',
			],
		];

		$parsedResponse = RpcClient::parseResponse(json_encode($testResponse));

		foreach ($testResponse as $i => $testCallResponse) {
			$callResponse = $parsedResponse[$i];

			$this->assertEquals($testCallResponse['id'], $callResponse->getId());
			$this->assertEquals($testCallResponse['error'], $callResponse->getError());
			$this->assertEquals($testCallResponse['result'], $callResponse->getResult());
		}
	}

	public function testResponseException()
	{
		$this->expectException(BadRemoteCallException::class);
		RpcClient::checkCallResponseError(new CallResponse([
			'result' => 100.00000000,
			'error' => 'Very bad error',
			'id' => 'first',
		]));
	}
}