<?php
namespace app\components\bitcoinRpc;

use Datto\JsonRpc\Http\Client as HttpClient;
use Datto\JsonRpc\Response;

class Client implements ClientInterface
{
	protected $user;
	protected $password;
	protected $host;
	protected $port;
	protected $client;

	public function __construct(string $user, string $password, string $host, int $port)
	{
		$this->user = $user;
		$this->password = $password;
		$this->host = $host;
		$this->port = $port;

		$this->client = new HttpClient($this->host.':'.$this->port, [
			'Authorization: Basic '.base64_encode($this->user.':'.$this->password),
		]);
	}

	public function getUser(): string
	{
		return $this->user;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function getHost(): string
	{
		return $this->host;
	}

	public function getPort(): int
	{
		return $this->port;
	}

	public function query(string $id, string $method, $arguments = null) : ClientInterface
	{
		$this->client->query($id, $method, $arguments);

		return $this;
	}

	/**
	 * @return Response[]
	 */
	public function send() : array
	{
		return $this->client->send();
	}
}