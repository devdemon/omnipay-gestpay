<?php

namespace Omnipay\GestPay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * GestPay Complete Purchase Request
 *
 * @method \Omnipay\GestPay\Message\CompletePurchaseResponse send()
 */
class CompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = $_GET['gestpay'];

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}