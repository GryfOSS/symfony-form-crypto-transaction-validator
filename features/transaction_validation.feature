Feature: Cryptocurrency Transaction Validation
    In order to validate cryptocurrency transactions
    As a user
    I need to submit transaction hashes through a Symfony form and verify them against real networks

    Background:
        Given I have a Symfony application with crypto validators
        And I have configured Etherscan API access

    Scenario: Validate a real Tron transaction hash
        Given I am on the transaction validation form
        When I select "tron" as the crypto type
        And I enter "5f9dda478de7176e7ec76428b28053fe5b3cab9d206ac737b6eecb5b6e521861" as the transaction hash
        And I submit the form
        Then the form should be valid
        And I should see "Transaction is valid"

    Scenario: Validate a real Ethereum transaction hash
        Given I am on the transaction validation form
        When I select "ethereum" as the crypto type
        And I enter "0x8a8dd2d1852d43288ec55ae3bab6af7bb58f7dcae7c1ecbfd4f439f5e9d9b241" as the transaction hash
        And I submit the form
        Then the form should be valid
        And I should see "Transaction is valid"

    Scenario: Reject invalid Tron transaction hash - wrong format
        Given I am on the transaction validation form
        When I select "tron" as the crypto type
        And I enter "invalid_tron_hash_12345" as the transaction hash
        And I submit the form
        Then the form should be invalid
        And I should see "Transaction validation failed"

    Scenario: Reject invalid Ethereum transaction hash - wrong format
        Given I am on the transaction validation form
        When I select "ethereum" as the crypto type
        And I enter "invalid_eth_hash" as the transaction hash
        And I submit the form
        Then the form should be invalid
        And I should see "Transaction validation failed"

    Scenario: Reject Ethereum hash format for Tron
        Given I am on the transaction validation form
        When I select "tron" as the crypto type
        And I enter "0x8a8dd2d1852d43288ec55ae3bab6af7bb58f7dcae7c1ecbfd4f439f5e9d9b241" as the transaction hash
        And I submit the form
        Then the form should be invalid
        And I should see "Transaction validation failed"

    Scenario: Reject Tron hash format for Ethereum
        Given I am on the transaction validation form
        When I select "ethereum" as the crypto type
        And I enter "5f9dda478de7176e7ec76428b28053fe5b3cab9d206ac737b6eecb5b6e521861" as the transaction hash
        And I submit the form
        Then the form should be invalid
        And I should see "Transaction validation failed"
