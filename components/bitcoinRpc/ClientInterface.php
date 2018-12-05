<?php
namespace app\components\bitcoinRpc;

interface ClientInterface
{
    function getHost() : string;
    function getPassword() : string;
    function getPort() : int;
    function getUser() : string;
    function addCall(string $id, string $method, array $arguments = []) : ClientInterface;

    /**
     * @return CallResponseInterface[]
     */
    function send() : array;
}