<?php
namespace app\components\bitcoinRpc;

interface ClientInterface
{
	function getHost() : string;
	function getPassword() : string;
	function getPort() : int;
	function getUser() : string;
	function query(string $id, string $method, $arguments = null) : ClientInterface;
	function send() : array;
}