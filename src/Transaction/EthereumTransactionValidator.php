<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Transaction;

/**
 * Ethereum transaction validator.
 *
 * This class validates Ethereum transaction hashes by checking their format
 * and verifying their existence on the Ethereum blockchain using the Etherscan API v2.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Transaction
 */
class EthereumTransactionValidator implements CryptoTransactionValidator
{
    private const ETHERSCAN_API_URL = 'https://api.etherscan.io/v2/api';
    private const ETHEREUM_MAINNET_CHAIN_ID = 1;

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
     * 3. Validates hex format (only contains valid hex characters)
     * 4. Queries Etherscan API v2 to verify the transaction exists and has successful status
     *
     * @param string $transactionHash The Ethereum transaction hash to validate
     * @return bool True if the transaction is valid and exists, false otherwise
     */
    public function isValid(string $transactionHash): bool
    {
        // Basic format validation
        if (!str_starts_with($transactionHash, '0x')) {
            return false;
        }

        if (mb_strlen($transactionHash) !== 66) {
            return false;
        }

        // Validate hex format (after 0x prefix)
        $hexPart = substr($transactionHash, 2);
        if (!ctype_xdigit($hexPart)) {
            return false;
        }

        // Check transaction receipt status using Etherscan API v2
        return $this->checkTransactionReceiptStatus($transactionHash);
    }

    /**
     * Checks transaction receipt status using Etherscan API v2.
     *
     * Uses the gettxreceiptstatus endpoint to verify:
     * - Transaction exists on the blockchain
     * - Transaction has been successfully executed (status = 1)
     *
     * @param string $transactionHash The transaction hash to check
     * @return bool True if transaction exists and is successful, false otherwise
     */
    private function checkTransactionReceiptStatus(string $transactionHash): bool
    {
        $url = self::ETHERSCAN_API_URL . '?' . http_build_query([
            'chainid' => self::ETHEREUM_MAINNET_CHAIN_ID,
            'module' => 'transaction',
            'action' => 'gettxreceiptstatus',
            'txhash' => $transactionHash,
            'apikey' => $this->etherscanApiKey
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; EthereumTransactionValidator/1.0)',
                'header' => [
                    'Accept: application/json',
                    'Content-Type: application/json'
                ]
            ]
        ]);

        try {
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                // Network error - consider transaction invalid
                return false;
            }

            $data = json_decode($response, true);

            if (!is_array($data)) {
                // Invalid JSON response
                return false;
            }

            if (!isset($data['status'])) {
                // Missing status field
                return false;
            }

            // Check if API call was successful
            if ($data['status'] !== '1') {
                // API error or transaction not found
                return false;
            }

            if (!isset($data['result'])) {
                // Missing result field
                return false;
            }

            // Check if transaction was successful (status = 1 means success)
            return isset($data['result']['status']) && $data['result']['status'] === '1';
        } catch (\Exception $e) {
            // Any unexpected error - consider transaction invalid
            return false;
        }
    }
}
