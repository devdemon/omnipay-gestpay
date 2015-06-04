<?php

namespace Omnipay\GestPay\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        if ($this->data->getErrorCode() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getTransactionReference()
    {
        return isset($this->data->BankTransactionID) ? $this->data->BankTransactionID : null;
    }

    public function getMessage()
    {
        return $this->data->getErrorCode() . ': ' . $this->data->getErrorDescription();
    }
}