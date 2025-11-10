# Symfony Form Crypto Transaction Validator

[![Tests](https://github.com/GryfOSS/symfony-form-crypto-transaction-validator/workflows/Tests/badge.svg)](https://github.com/GryfOSS/symfony-form-crypto-transaction-validator/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A Symfony Form validation library for cryptocurrency addresses and transaction hashes. This library provides custom validation constraints to validate cryptocurrency addresses and verify transaction hashes against their respective blockchain networks.

## 📋 Purpose

This library enables Symfony applications to:
- **Validate cryptocurrency wallet addresses** with proper format checking
- **Verify transaction hashes** against real blockchain networks
- **Integrate seamlessly** with Symfony Form components
- **Support multiple cryptocurrencies** with a unified interface

Perfect for applications that need to validate user-provided cryptocurrency data, such as:
- Payment processors
- Cryptocurrency exchanges
- Wallet applications
- DeFi platforms

## 🪙 Supported Cryptocurrencies

### Currently Supported

| Cryptocurrency | Address Validation | Transaction Validation | Network Verification |
|---------------|-------------------|----------------------|---------------------|
| **Ethereum (ETH)** | ✅ Format + Checksum | ✅ Hash format + API | ✅ Etherscan API v2 |
| **Tron (TRX)** | ✅ Base58 + Validation | ✅ Hash format + API | ✅ TronGrid API |

### Address Validation Features
- **Ethereum**: EIP-55 checksum validation, proper hex format, length validation
- **Tron**: Base58 encoding validation, checksum verification, proper format

### Transaction Validation Features
- **Ethereum**: 0x-prefixed hex format, 66-character length, network existence check
- **Tron**: 64-character hex format, network existence verification

## 🚀 Future Plans

### Upcoming Cryptocurrency Support
We plan to add support for more cryptocurrencies:
- **Bitcoin (BTC)** - Address validation and transaction verification
- **Litecoin (LTC)** - P2PKH, P2SH, and Bech32 address formats
- **Cardano (ADA)** - Byron and Shelley address formats
- **Polkadot (DOT)** - SS58 address format validation
- **Binance Smart Chain (BSC)** - Ethereum-compatible validation
- **Polygon (MATIC)** - Ethereum-compatible validation

### Technical Improvements
- **HTTP Client Integration**: Replace `file_get_contents()` with proper HTTP client (Guzzle/Symfony HTTP Client) in Ethereum validator for:
  - Better error handling
  - Timeout configuration
  - Request/response middleware
  - Retry mechanisms
  - Connection pooling

- **Async Validation**: Support for asynchronous transaction verification
- **Caching Layer**: Add caching for API responses to reduce external calls
- **Rate Limiting**: Built-in rate limiting for API calls
- **Mock Providers**: Test-friendly mock implementations

## 📦 Installation

```bash
composer require gryfoss/symfony-form-crypto-transaction-validator
```

## 🛠️ Usage

### Basic Address Validation

```php
<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;

class PaymentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wallet_address', TextType::class, [
                'constraints' => [
                    new CryptoAddress(SupportedCryptoEnum::ETHEREUM)
                ]
            ]);
    }
}
```

### Dynamic Address Validation Based on Selected Crypto

```php
<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;

class DynamicCryptoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Ethereum' => 'ethereum',
                    'Tron' => 'tron',
                ],
                'placeholder' => 'Choose cryptocurrency'
            ])
            ->add('address', TextType::class);

        // Add dynamic constraint based on selected crypto type
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (isset($data['crypto_type'])) {
                $cryptoEnum = match($data['crypto_type']) {
                    'ethereum' => SupportedCryptoEnum::ETHEREUM,
                    'tron' => SupportedCryptoEnum::TRON,
                    default => null
                };

                if ($cryptoEnum) {
                    $form->add('address', TextType::class, [
                        'constraints' => [
                            new CryptoAddress($cryptoEnum)
                        ]
                    ]);
                }
            }
        });
    }
}
```

### Transaction Hash Validation

```php
<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;

class TransactionVerificationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ethereum_transaction', TextType::class, [
                'label' => 'Ethereum Transaction Hash',
                'constraints' => [
                    new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM)
                ],
                'help' => 'Enter a valid Ethereum transaction hash (0x...)'
            ])
            ->add('tron_transaction', TextType::class, [
                'label' => 'Tron Transaction Hash',
                'constraints' => [
                    new CryptoTransactionHash(SupportedCryptoEnum::TRON)
                ],
                'help' => 'Enter a valid Tron transaction hash'
            ]);
    }
}
```

### Using the Validator Factory

```php
<?php

use GryfOSS\CryptocurrenciesFormValidator\Factory\CryptoTransactionValidatorFactory;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;

// Configure required parameters for each currency.
$config = [
    'eth' => 'your-etherscan-api-key',
    'trx' => 'https://api.trongrid.io' // optional
];

$factory = new CryptoTransactionValidatorFactory($config);

// Validate Ethereum transaction
$ethereumValidator = $factory->createValidator(SupportedCryptoEnum::ETHEREUM);
$isValid = $ethereumValidator->isValid('0x8a8dd2d1852d43288ec55ae3bab6af7bb58f7dcae7c1ecbfd4f439f5e9d9b241');

// Validate Tron transaction
$tronValidator = $factory->createValidator(SupportedCryptoEnum::TRON);
$isValid = $tronValidator->isValid('5f9dda478de7176e7ec76428b28053fe5b3cab9d206ac737b6eecb5b6e521861');
```

### Standalone Address Validation

```php
<?php

use GryfOSS\CryptocurrenciesFormValidator\Address\EthereumAddress;
use GryfOSS\CryptocurrenciesFormValidator\Address\TronAddress;

// Ethereum address validation
$ethAddress = new EthereumAddress();
$isValid = $ethAddress->isValid('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed');

// Tron address validation
$tronAddress = new TronAddress();
$isValid = $tronAddress->isValid('TLyqzVGLV1srkB7dToTAEqgDSfPtXRJZYH');
```

## ⚙️ Configuration

### Environment Variables

For transaction validation, you need API access:

```bash
# .env file
ETHERSCAN_API_KEY=your-etherscan-api-key-here
```

## 🧪 Testing

```bash
# Run unit tests
./vendor/bin/phpunit

# Run functional tests
./vendor/bin/behat

# Run with coverage
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage
```

## 📊 Requirements

- **PHP**: 8.2 or higher
- **Symfony**: 7.3 or higher (Form and Validator components)

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### 🐛 Bug Reports
Found a bug? Please [open an issue](https://github.com/GryfOSS/symfony-form-crypto-transaction-validator/issues) with:
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- PHP and Symfony versions

### 💡 Feature Requests
Have an idea for improvement? [Create an issue](https://github.com/GryfOSS/symfony-form-crypto-transaction-validator/issues) with:
- Description of the feature
- Use case examples
- Implementation suggestions (if any)

### 🔧 Pull Requests
Ready to contribute code?

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/amazing-feature`
3. **Write tests** for your changes
4. **Ensure tests pass**: `./vendor/bin/phpunit && ./vendor/bin/behat`
5. **Check code coverage**: Coverage must be ≥80%
6. **Follow PSR-12** coding standards
7. **Commit your changes**: `git commit -m 'Add amazing feature'`
8. **Push to your fork**: `git push origin feature/amazing-feature`
9. **Open a Pull Request**

### 🎯 Contribution Ideas
- Add support for new cryptocurrencies
- Improve error messages and validation feedback
- Add caching layer for API responses
- Implement HTTP client for better network handling
- Add more comprehensive test scenarios
- Improve documentation and examples

### 📝 Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/symfony-form-crypto-transaction-validator.git
cd symfony-form-crypto-transaction-validator

# Install dependencies
composer install

# Copy environment file
cp .env .env.local
# Edit .env.local with your API keys

# Run tests to ensure everything works
./vendor/bin/phpunit
./vendor/bin/behat
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Authors

- **IDCT Bartosz Pachołek** - [bartosz+github@idct.tech](mailto:bartosz+github@idct.tech)

## 🙏 Acknowledgments

- Symfony team for the excellent Form and Validator components
- Cryptocurrency communities for address and transaction format specifications
- Contributors and users who help improve this library

---

**⭐ Star this repository if you find it useful!**

For questions, suggestions, or support, please [open an issue](https://github.com/GryfOSS/symfony-form-crypto-transaction-validator/issues) on GitHub.