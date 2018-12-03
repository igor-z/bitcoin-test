<?php
namespace app\components\bitcoinRpc;

class CallResponse implements CallResponseInterface
{
	protected $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getResult()
	{
		return $this->data['result'] ?? null;
	}

	public function getError()
	{
		return $this->data['error'] ?? null;
	}

	public function getId()
	{
		return $this->data['id'] ?? null;
	}
}