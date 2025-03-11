<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\OrderPay\Provider;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use BitBag\SyliusPayUPlugin\Exception\PayUException;
use Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CaptureHttpResponseProvider implements HttpResponseProviderInterface
{

    public function supports(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest,): bool
    {
        return $paymentRequest->getState() === PaymentRequestInterface::STATE_PROCESSING;
    }

    public function getResponse(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest,): Response
    {
        $data = $paymentRequest->getResponseData();
        $result = $data['result'];
        Assert::notNull($result, 'Result is null.');
        $response = $result->getResponse();

        if (!$response) {
            throw new \LogicException('Response is null.');
        }

        if (OpenPayUBridgeInterface::SUCCESS_API_STATUS !== $response->status->statusCode) {
            throw PayUException::newInstance($response->status);
        }
        $url = $response->redirectUri;

        return new RedirectResponse($url, Response::HTTP_SEE_OTHER);
    }
}
