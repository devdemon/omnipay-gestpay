<?php

namespace Omnipay\GestPay;

use Omnipay\Common\AbstractGateway;

/**
 * GestPay Gateway
 *
 * @link https://www.gestpay.it/gestpay/specifiche-tecniche/index.jsp
 */
class Gateway extends AbstractGateway
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'GestPay';
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'shopLoginId' => '',
            'testMode' => false,
        );
    }

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

    /**
     * @param  array                                   $parameters
     * @return \Omnipay\GestPay\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GestPay\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param  array                                           $parameters
     * @return \Omnipay\GestPay\Message\CompletePurchaseRequest
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\GestPay\Message\CompletePurchaseRequest', $parameters);
    }
}