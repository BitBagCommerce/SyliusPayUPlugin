<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PayUContext implements Context
{
    use MockeryPHPUnitIntegration;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ExampleFactoryInterface */
    private $paymentMethodExampleFactory;

    /** @var FactoryInterface */
    private $paymentMethodTranslationFactory;

    /** @var ObjectManager */
    private $paymentMethodManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExampleFactoryInterface $paymentMethodExampleFactory,
        ObjectManager $paymentMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodExampleFactory = $paymentMethodExampleFactory;
        $this->paymentMethodManager = $paymentMethodManager;
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and PayU Checkout gateway
     */
    public function theStoreHasAPaymentMethodWithACodeAndPayuCheckoutGateway(
        string $paymentMethodName,
        string $paymentMethodCode
    ): void {
        $paymentMethod = $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, 'PayU Checkout');
        $paymentMethod->getGatewayConfig()->setConfig(
            [
                'environment' => 'sandbox',
                'signature_key' => 'TEST',
                'pos_id' => 'TEST',
                'oauth_client_id' => 'CLIENT_ID',
                'oauth_client_secret' => 'SECRET',
                'payum.http_client' => '@sylius.payum.http_client',
            ]
        );
        $this->paymentMethodManager->persist($paymentMethod);
        $this->paymentMethodManager->flush();
    }

    private function createPaymentMethod(
        string $name,
        string $code,
        string $description = '',
        bool $addForCurrentChannel = true,
        ?int $position = null
    ): PaymentMethodInterface {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create(
            [
                'name' => ucfirst($name),
                'code' => $code,
                'description' => $description,
                'gatewayName' => 'payu',
                'gatewayFactory' => 'payu',
                'enabled' => true,
                'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel'))
                    ? [$this->sharedStorage->get('channel')] : [],
            ]
        );

        if (null !== $position) {
            $paymentMethod->setPosition($position);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}
