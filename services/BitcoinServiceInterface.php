<?php
namespace app\services;

use app\DTOs\BalanceDetails;
use app\DTOs\CredentialsDTO;

interface BitcoinServiceInterface
{
    function createAddress() : string;
    function getBalanceDetails() : BalanceDetails;
    function getCredentials() : CredentialsDTO;
}