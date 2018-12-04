<?php
namespace app\components\bitcoinRpc;

use Exception;

class BadRemoteCallException extends Exception
{
	public function __construct(CallResponseInterface $response)
	{
		parent::__construct($response->getError());
	}
}