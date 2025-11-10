Feature: Cryptocurrency Address Validation
    In order to validate cryptocurrency addresses
    As a user
    I need to submit addresses through a Symfony form

    Background:
        Given I have a Symfony application with crypto validators

    Scenario: Validate a valid Tron address
        Given I am on the address validation form
        When I select "tron" as the crypto type
        And I enter "TLyqzVGLV1srkB7dToTAEqgDSfPtXRJZYH" as the address
        And I submit the form
        Then the form should be valid
        And I should see "Address is valid"

    Scenario: Validate another valid Tron address
        Given I am on the address validation form
        When I select "tron" as the crypto type
        And I enter "TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t" as the address
        And I submit the form
        Then the form should be valid
        And I should see "Address is valid"

    Scenario: Validate a valid Ethereum address
        Given I am on the address validation form
        When I select "ethereum" as the crypto type
        And I enter "0x5aae5775959fbc2557cc8789bc1bf90a239d9c91" as the address
        And I submit the form
        Then the form should be valid
        And I should see "Address is valid"

    Scenario: Validate another valid Ethereum address
        Given I am on the address validation form
        When I select "ethereum" as the crypto type
        And I enter "0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed" as the address
        And I submit the form
        Then the form should be valid
        And I should see "Address is valid"

    Scenario: Reject invalid Tron address - wrong length
        Given I am on the address validation form
        When I select "tron" as the crypto type
        And I enter "TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMb" as the address
        And I submit the form
        Then the form should be invalid
        And I should see "Address validation failed"

    Scenario: Reject invalid Ethereum address - wrong format
        Given I am on the address validation form
        When I select "ethereum" as the crypto type
        And I enter "invalid_ethereum_address" as the address
        And I submit the form
        Then the form should be invalid
        And I should see "Address validation failed"

    Scenario: Reject Ethereum address when Tron is selected
        Given I am on the address validation form
        When I select "tron" as the crypto type
        And I enter "0x5aae5775959fbc2557cc8789bc1bf90a239d9c91" as the address
        And I submit the form
        Then the form should be invalid
        And I should see "Address validation failed"

    Scenario: Reject Tron address when Ethereum is selected
        Given I am on the address validation form
        When I select "ethereum" as the crypto type
        And I enter "TLyqzVGLV1srkB7dToTAEqgDSfPtXRJZYH" as the address
        And I submit the form
        Then the form should be invalid
        And I should see "Address validation failed"