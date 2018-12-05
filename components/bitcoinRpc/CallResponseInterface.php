<?php
namespace app\components\bitcoinRpc;

interface CallResponseInterface
{
    function getResult();
    function getError();
    function getId();
}