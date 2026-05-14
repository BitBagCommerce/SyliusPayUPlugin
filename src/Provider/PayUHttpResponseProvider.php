<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider;

use Sylius\Bundle\CoreBundle\OrderPay\Provider\FinalUrlProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class PayUHttpResponseProvider implements HttpResponseProviderInterface
{
    public function __construct(
        private readonly FinalUrlProviderInterface $finalUrlProvider,
    ) {
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): bool {
        return true;
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        if ($paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE) {
            $redirectUri = $paymentRequest->getResponseData()['redirectUri'] ?? null;

            if ($redirectUri !== null) {
                return new RedirectResponse((string) $redirectUri);
            }
        }

        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();

        return new RedirectResponse(
            $this->finalUrlProvider->getUrl($payment),
        );
    }
}
