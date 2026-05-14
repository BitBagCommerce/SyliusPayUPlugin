<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Page\External;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PayUCheckoutPage extends Page implements PayUCheckoutPageInterface
{
    public function __construct(
        Session $session,
        $parameters,
        private readonly PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
        parent::__construct($session, $parameters);
    }

    public function pay(): void
    {
        $this->postWebhook();
    }

    public function cancel(): void
    {
        $this->postWebhook();
    }

    protected function getUrl(array $urlParameters = []): string
    {
        return '/';
    }

    private function postWebhook(): void
    {
        $paymentRequest = $this->findActivePaymentRequest();
        $hash = (string) $paymentRequest->getHash();

        $body = (string) json_encode(['order' => ['orderId' => '1']]);
        $signature = md5($body . 'TEST');
        $signatureHeader = 'sender=payu;signature=' . $signature . ';algorithm=MD5;content=DOCUMENT';

        /** @var BrowserKitDriver $driver */
        $driver = $this->getDriver();
        $driver->getClient()->request(
            'POST',
            '/payment-requests/' . $hash,
            [],
            [],
            ['HTTP_OPENPAYU_SIGNATURE' => $signatureHeader, 'CONTENT_TYPE' => 'application/json'],
            $body,
        );

        $driver->getClient()->request('GET', '/en_US/order/after-pay/' . $hash);
    }

    private function findActivePaymentRequest(): PaymentRequestInterface
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->findOneBy([
            'state' => PaymentRequestInterface::STATE_PROCESSING,
            'action' => PaymentRequestInterface::ACTION_CAPTURE,
        ]);

        if (null === $paymentRequest) {
            throw new \RuntimeException('Cannot find processing capture payment request. Make sure you are past the order confirmation step.');
        }

        return $paymentRequest;
    }
}
