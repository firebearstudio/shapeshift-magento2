<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model\Client;

use Firebear\ShapeShift\Helper\Data;
use Psr\Log\LoggerInterface;

class ShapeShiftClientApi
{
    private $helper;
    private $pair;
    public $depoAddress;
    public $depoAmount;
    private $log;
    public $error;

    public function __construct(Data $helper, LoggerInterface $log)
    {
        $this->helper = $helper;
        $this->log    = $log;
    }

    public function sendFixedAmount($amount, $withdrawAdd, $returnAdd, $inputCrypto, $outputCrypto)
    {
        $this->log->info("sendFixedAmount() 1");
        $this->pairing($inputCrypto, $outputCrypto);
        $this->log->info("sendFixedAmount() 2");
        $data = [
            "amount"        => $amount,
            "withdrawal"    => $withdrawAdd,
            "pair"          => $this->pair,
            "returnAddress" => $returnAdd,
            "apiKey"        => $this->helper->getGeneralConfig('apikey')
        ];
        $this->log->info("sendFixedAmount() 3");
        $responseArray = $this->sendReqestPost($this->helper->getGeneralConfig('sendamount'), json_encode($data));
        $this->log->info("sendFixedAmount() DATA: ", $responseArray);
        if (isset($responseArray['error'])) {
            $this->setError(
                $responseArray['error'],
                $this->helper->getGeneralConfig('sendamount')
            );
        } else {
            if (!isset($responseArray['success']['deposit'])) {
                $this->setError(
                    'Payment method is not available. Deposit address not set.',
                    $this->helper->getGeneralConfig('sendamount')
                );
            } else {
                $this->depoAddress = $responseArray['success']['deposit'];
                $this->depoAmount  = $responseArray['success']['depositAmount'];
            }
        }

    }

    public function getAvailableCurrency($versionArray = '')
    {
        $responseArray             = $this->sendReqestGet($this->helper->getGeneralConfig('getcoins'));
        $arrayAvailableCurrency    = [];
        $arrayAvailableCurrencyAll = [];
        foreach ($responseArray as $k => $currency) {
            if ($versionArray == 'adminhtml') {
                $arrayAvailableCurrency[] = ['label' => $k, 'value' => strtolower($k)];
            } else {
                $arrayAvailableCurrencyAll[strtolower($k)] = $k;
            }
        }

        if ($versionArray != 'adminhtml') {
            $currencyAvailable = explode(',', $this->helper->getGeneralConfig('allowcurrency'));
            if ($this->helper->getGeneralConfig('allowcurrency')) {
                foreach ($currencyAvailable as $currency) {
                    $arrayAvailableCurrency[$currency] = strtoupper($currency);
                }
            } else {
                $arrayAvailableCurrency = $arrayAvailableCurrencyAll;
            }
        }


        return $arrayAvailableCurrency;
    }

    public function getPaymentDescription()
    {
        return $this->helper->getGeneralConfig('description');
    }

    public function getCurrencyFullName($code)
    {
        $responseArray = $this->sendReqestGet($this->helper->getGeneralConfig('getcoins'));

        return $responseArray[strtoupper($code)]['name'];
    }

    public function getStatus($depositAddress)
    {
        $responseArray = $this->sendReqestGet($this->helper->getGeneralConfig('txstatus') . $depositAddress);

        return $responseArray['status'];
    }

    private function sendReqestPost($url, $data)
    {
        $ch = $this->getCurl();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $data
        );

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: application/json"
            )
        );

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function sendReqestGet($url)
    {
        $ch = $this->getCurl();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function getCurl()
    {
        return curl_init();
    }

    private function pairing($inputCrypto, $outputCrypto)
    {
        $this->pair = $inputCrypto . "_" . $outputCrypto;
    }

    private function setError($error, $url)
    {
        $this->error['error'] = $error;
        $this->error['url']   = $url;
    }
}