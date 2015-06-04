<?php

namespace Omnipay\GestPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Exception\InvalidResponseException;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        if (!$this->data) {
            throw new InvalidResponseException();
        }

        if ($this->data->getErrorCode() != 0) {
            throw new InvalidResponseException($this->data->getErrorDescription());
        }

        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->data->getRedirectUrl();
    }

    public function getRedirectMethod()
    {
        return 'GET';
    }

    public function getRedirectData()
    {
        return array();
    }
}