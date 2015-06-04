<?php

namespace Omnipay\GestPay\Message;

use SoapClient, SimpleXMLElement;

class PurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://ecommS2S.sella.it/gestpay/GestPayWS/WsCryptDecrypt.asmx?wsdl';
    protected $testEndpoint = 'https://test.sella.it/gestpay/GestPayWS/WsCryptDecrypt.asmx?wsdl';

    public function getData()
    {
        $this->validate('amount', 'description', 'returnUrl');

        // Currency
        $uicCode = 242;
        switch (strtoupper($this->getCurrency())) {
            case 'AUD':
                $uicCode = '109';
                break;
            case 'BRL':
                $uicCode = '234';
                break;
            case 'CAD':
                $uicCode = '12';
                break;
            case 'CHF':
                $uicCode = '3';
                break;
            case 'CNY':
                $uicCode = '144';
                break;
            case 'CZK':
                $uicCode = '223';
                break;
            case 'DKK':
                $uicCode = '7';
                break;
            case 'EUR':
                $uicCode = '242';
                break;
            case 'GBP':
                $uicCode = '2';
                break;
            case 'HKD':
                $uicCode = '103';
                break;
            case 'HUF':
                $uicCode = '153';
                break;
            case 'ITL':
                $uicCode = '18';
                break;
            case 'JPY':
                $uicCode = '71';
                break;
            case 'NOK':
                $uicCode = '8';
                break;
            case 'PLN':
                $uicCode = '237';
                break;
            case 'RUB':
                $uicCode = '244';
                break;
            case 'SEK':
                $uicCode = '9';
                break;
            case 'SGD':
                $uicCode = '124';
                break;
            case 'USD':
                $uicCode = '1';
                break;
        }

        /*
        $data = array();
        $data['shopLogin'] = 'GESPAY63415';//$this->getShopLoginId();
        $data['uicCode'] = 242;
        $data['amount'] = $this->getAmount();
        $data['shopTransactionId'] = $this->getTransactionId();

        //----------------------------------------
        // Optional
        //----------------------------------------
        if ($this->getCard()) {
            $data['buyerName'] = $this->getCard()->getName();
            $data['buyerEmail'] = $this->getCard()->getEmail();
        }



        $data['uicCode'] = 242;
        */

        require_once __DIR__."/../sdk/GestPayCryptWS.php";
        $gestpay = new \GestPayCryptWS();
        $gestpay->setShopLogin($this->getShopLoginId());
        $gestpay->setShopTransactionID($this->getTransactionReference());
        $gestpay->setAmount($this->getAmount());
        $gestpay->setCurrency($uicCode);

        return $gestpay;
    }

    public function sendData($gestpay)
    {
        if ($this->getTestMode()) {
            $gestpay->setTestEnv(true);
        }

        // Execute
        $gestpay->encrypt();

        return $this->response = new PurchaseResponse($this, $gestpay);
    }
}