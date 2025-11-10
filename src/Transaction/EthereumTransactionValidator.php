<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Transaction;

use Etherscan\Client;

/**
 * Ethereum transaction validator.
 *
 * This class validates Ethereum transaction hashes by checking their format
 * and verifying their existence on the Ethereum blockchain using the Etherscan API.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Transaction
 */
class EthereumTransactionValidator implements CryptoTransactionValidator
{
    /**
     * Constructor for the Ethereum transaction validator.
     *
     * @param string $etherscanApiKey The API key for Etherscan API requests
     */
    public function __construct(protected string $etherscanApiKey)
    {

    }

    /**
     * Validates an Ethereum transaction hash.
     *
     * Performs validation in multiple steps:
     * 1. Checks if the hash starts with '0x' prefix
     * 2. Validates the length (must be 66 characters total)
     * 3. Queries Etherscan API to verify the transaction exists
     *
     * @param string $transactionHash The Ethereum transaction hash to validate
     * @return bool True if the transaction is valid and exists, false otherwise
     */
    public function isValid(string $transactionHash): bool
    {
        if (!str_starts_with($transactionHash, '0x')) {
            return false;
        }

        if (mb_strlen($transactionHash) !== 66) {
            return false;
        }

        $etherscanClient = new Client($this->etherscanApiKey);
        $transactionReceipt = $etherscanClient->api('proxy')->getTransactionReceipt($transactionHash);

        $isValid = isset($transactionReceipt['result']['transactionHash']) && $transactionReceipt['result']['transactionHash'] === $transactionHash;

        return $isValid;
    }
}
