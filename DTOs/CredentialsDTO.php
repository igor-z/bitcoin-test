<?php
namespace app\DTOs;

class CredentialsDTO
{
	private $password;
	private $host;
	private $userName;
	private $port;

	public function __construct(string $host, string $userName, string $password, int $port)
	{
		$this->password = $password;
		$this->host = $host;
		$this->userName = $userName;
		$this->port = $port;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getUserName()
	{
		return $this->userName;
	}

	public function getPort()
	{
		return $this->port;
	}
}