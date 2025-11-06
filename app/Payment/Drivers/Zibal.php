<?php

namespace App\Payment\Drivers;

use GuzzleHttp\Client;
use Shetabit\Multipay\Abstracts\Driver;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Contracts\ReceiptInterface;
use Shetabit\Multipay\Invoice;
use Shetabit\Multipay\Receipt;
use Shetabit\Multipay\RedirectionForm;
use Shetabit\Multipay\Request;

class Zibal extends Driver
{
    /**
     * Invoice
     *
     * @var Invoice
     */
    protected $invoice;

    /**
     * Driver settings
     *
     * @var object
     */
    protected $settings;

    /**
     * Zibal constructor.
     * Construct the class with the relevant settings.
     *
     * @param Invoice $invoice
     * @param $settings
     */
    public function __construct(Invoice $invoice, $settings)
    {
        $this->invoice($invoice);
        $this->settings = (object) $settings;
    }

    /**
     * Purchase Invoice.
     *
     * @return string
     *
     * @throws PurchaseFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function purchase()
    {
        $data = [
            'merchant' => $this->settings->merchantId,
            'amount' => $this->invoice->getAmount(),
            'callbackUrl' => $this->settings->callbackUrl,
            'description' => $this->settings->description ?? 'خرید از فروشگاه',
        ];

        $client = new Client();
        $response = $client->post(
            $this->settings->apiPurchaseUrl,
            [
                'json' => $data,
                'http_errors' => false,
            ]
        );

        $body = json_decode($response->getBody()->getContents(), true);

        if (!isset($body['result']) || $body['result'] != 100) {
            throw new PurchaseFailedException($body['message'] ?? 'خطا در ایجاد تراکنش');
        }

        $this->invoice->transactionId($body['trackId']);

        return $this->invoice->getTransactionId();
    }

    /**
     * Pay the Invoice
     *
     * @return RedirectionForm
     */
    public function pay(): RedirectionForm
    {
        $payUrl = $this->settings->apiPaymentUrl . $this->invoice->getTransactionId();

        return $this->redirectWithForm($payUrl, [], 'GET');
    }

    /**
     * Verify payment
     *
     * @return ReceiptInterface
     *
     * @throws InvalidPaymentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verify(): ReceiptInterface
    {
        $trackId = $this->invoice->getTransactionId() ?? Request::input('trackId');

        $data = [
            'merchant' => $this->settings->merchantId,
            'trackId' => $trackId,
        ];

        $client = new Client();
        $response = $client->post(
            $this->settings->apiVerificationUrl,
            [
                'json' => $data,
                'http_errors' => false,
            ]
        );

        $body = json_decode($response->getBody()->getContents(), true);

        if (!isset($body['result']) || $body['result'] != 100) {
            throw new InvalidPaymentException($body['message'] ?? 'تراکنش ناموفق بود');
        }

        return $this->createReceipt($body['refNumber']);
    }

    /**
     * Generate the payment's receipt
     *
     * @param $referenceId
     *
     * @return Receipt
     */
    protected function createReceipt($referenceId)
    {
        $receipt = new Receipt('zibal', $referenceId);

        return $receipt;
    }
}
