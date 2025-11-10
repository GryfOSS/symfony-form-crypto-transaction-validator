<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'secret' => 'test',
            'form' => true,
            'validation' => [
                'enable_attributes' => true,
            ],
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);

        $container->loadFromExtension('twig', [
            'default_path' => '%kernel.project_dir%/test-app/templates',
            'form_themes' => ['bootstrap_4_layout.html.twig'],
        ]);

        // Make services public for testing
        $container->setAlias('public.form.factory', 'form.factory')->setPublic(true);
        $container->setAlias('public.validator', 'validator')->setPublic(true);

        // Enable autoconfigure for our namespace
        $container->loadFromExtension('framework', [
            'property_access' => true,
        ]);

        // Auto-configure services in our namespace
        $container->autowire(\GryfOSS\CryptocurrenciesFormValidator\Factory\CryptoTransactionValidatorFactory::class)
            ->setArgument('$config', [
                'eth' => '%env(ETHERSCAN_API_KEY)%',  // Ethereum validator expects a string (API key)
                'trx' => 'https://api.trongrid.io'   // Tron validator expects a string (API host)
            ]);

        $container->autowire(\GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoAddressValidator::class)
            ->addTag('validator.constraint_validator');

        $container->autowire(\GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoTransactionHashValidator::class)
            ->addTag('validator.constraint_validator');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('crypto_form', '/crypto-form')
            ->controller('TestController::cryptoForm')
            ->methods(['GET', 'POST']);

        $routes->add('address_form', '/address-form')
            ->controller('TestController::addressForm')
            ->methods(['GET', 'POST']);

        $routes->add('transaction_form', '/transaction-form')
            ->controller('TestController::transactionForm')
            ->methods(['GET', 'POST']);
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }
}