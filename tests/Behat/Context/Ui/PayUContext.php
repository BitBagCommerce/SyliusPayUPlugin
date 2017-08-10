<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\PayUPlugin\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\BitBag\PayUPlugin\Behat\Mocker\PayUApiMocker;
use Tests\BitBag\PayUPlugin\Behat\Page\External\PayUCheckoutPageInterface;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class PayUContext implements Context
{
    /**
     * @var PayUApiMocker
     */
    private $payUApiMocker;

    /**
     * @var CompletePageInterface
     */
    private $summaryPage;

    /**
     * @var PayUCheckoutPageInterface
     */
    private $payUCheckoutPage;

    /**
     * @var ShowPageInterface
     */
    private $orderDetails;

    /**
     * @param CompletePageInterface $summaryPage
     * @param PayUApiMocker $payUApiMocker
     * @param PayUCheckoutPageInterface $payUCheckoutPage
     * @param ShowPageInterface $orderDetails
     */
    public function __construct(
        PayUApiMocker $payUApiMocker,
        CompletePageInterface $summaryPage,
        PayUCheckoutPageInterface $payUCheckoutPage,
        ShowPageInterface $orderDetails
    )
    {
        $this->orderDetails = $orderDetails;
        $this->payUCheckoutPage = $payUCheckoutPage;
        $this->summaryPage = $summaryPage;
        $this->payUApiMocker = $payUApiMocker;
    }

    /**
     * @When I confirm my order with PayU payment
     * @Given I have confirmed my order with PayU payment
     */
    public function iConfirmMyOrderWithPayuPayment()
    {
        $this->payUApiMocker->mockApiSuccessfulPaymentResponse(function (){
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @When I sign in to PayU and pay successfully
     */
    public function iSignInToPayuAndPaySuccessfully()
    {
        $this->payUApiMocker->completedPayment(function (){
            $this->payUCheckoutPage->pay();
        });
    }

    /**
     * @When I cancel my PayU payment
     * @Given I have cancelled PayU payment
     */
    public function iCancelMyPayuPayment()
    {
        $this->payUApiMocker->canceledPayment(function (){
            $this->payUCheckoutPage->cancel();
        });
    }

    /**
     * @When I try to pay again PayU payment
     */
    public function iTryToPayAgainPayuPayment()
    {
        $this->payUApiMocker->mockApiSuccessfulPaymentResponse(function (){
            $this->orderDetails->pay();
        });
    }
}