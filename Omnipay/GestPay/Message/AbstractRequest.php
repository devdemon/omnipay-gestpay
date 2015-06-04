<?php

namespace Omnipay\GestPay\Message;

use Guzzle\Common\Event;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://ecomm.sella.it/pagam/pagam.aspx';
    protected $testEndpoint = 'https://testecomm.sella.it/pagam/pagam.aspx';

    public function getShopLoginId()
    {
        return $this->getParameter('shopLoginId');
    }

    public function setShopLoginId($value)
    {
        return $this->setParameter('shopLoginId', $value);
    }

    public function getTestMode()
    {
        return $this->getParameter('testMode');
    }

    public function setTestMode($value)
    {
        return $this->setParameter('testMode', $value);
    }
}