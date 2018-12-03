<?php
namespace app\components\bitcoinRpc;

class CallResponse implements CallResponseInterface
{
	protected $result;
	protected $error;
	protected $id;

	public function __construct(array $data)
	{
		$this->id = $data['id'] ?? null;
		$this->error = $data['error'] ?? null;
		$this->result = $data['result'] ?? null;
	}

	public function getResult()
	{
		return $this->result;
	}

	public function getError()
	{
		return $this->error;
	}

	public function getId()
	{
		return $this->id;
	}
}