<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model\Client;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Adapter\Curl as CurlAdapter;
use Magento\Setup\Exception;
use Psr\Log\LoggerInterface;

class ShapeShiftClientApi implements \Firebear\ShapeShift\Api\ShapeShiftClientApiInterface
{
    private $curl;
    private $pair;
    private $status;
    private $amount;
    private $withdrawAdd;
    private $returnAdd;
    private $inputCrypto;
    private $outputCrypto;
    private $email;
    private $logger;
    public $depoAddress;
    private $rate;
    private $limit;
    private $rate_limit;
    private $shift;
    private $quoteRate;
    private $curlAdapter;

    public function __construct(Curl $curl, LoggerInterface $logger, CurlAdapter $curlAdapter)
    {
        $this->curl = $curl;
        $this->curlAdapter = $curlAdapter;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    private function xInput($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    /**
     * {@inheritdoc}
     */
    private function Set_Error($error, $curl, $shift = 0)
    {
        header('HTTP/1.1 500 Internal Server Booboo');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'ERROR', 'code' => 1337)));
    }

    /**
     * {@inheritdoc}
     */
    private function xCurl()
    {
        $curl = $this->curl;
        $curl->setOption(CURLOPT_ENCODING, 'gzip');
        $curl->setOption(CURLOPT_SSL_VERIFYPEER, true);
        $curl->setOption(CURLOPT_NOSIGNAL, 1);
        $curl->setOption(CURLOPT_TIMEOUT_MS, 1500);
        return $curl;
    }

    /**
     * {@inheritdoc}
     */
    private function Check()
    {
        if ($this->status === "SUCCESS" && isset($this->amount) && isset($this->withdrawAdd) && isset($this->pair) && isset($this->returnAdd) && isset($this->email)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Pairing($inputCrypto, $outputCrypto)
    {
        $this->status = "SUCCESS";
        $this->inputCrypto = strtolower($inputCrypto);
        $this->outputCrypto = strtolower($outputCrypto);
        $this->pair = $this->inputCrypto . "_" . $this->outputCrypto;
    }

    /**
     * {@inheritdoc}
     */
    public function Setup($withdrawAdd, $returnAdd, $email = "ded2.94@tut.by", $amount = 0)
    {
        $this->withdrawAdd = $this->xInput($withdrawAdd);
        $this->returnAdd = $this->xInput($returnAdd);
        $this->amount = $amount;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $this->xInput($email);
        } else {
            $this->email = "shapeshift@nepalbit.co.in";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Set_Depo_Add($depoAddress, $amount = 0)
    {
        $this->depoAddress = $depoAddress;
        if ($amount > 0) {
            $this->amount = $amount;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Rate()
    {
        $this->logger->info("(CLIENT API Rate()) 1");
        $curl = $this->xCurl();
        $this->logger->info("(CLIENT API Rate()) 2");
        $rate = 'https://shapeshift.io/rate/' . $this->pair;
        $this->logger->info("(CLIENT API Rate()) 3");
        $curl->get($rate, []);
        $curlDecode = json_decode($curl->getBody(), true);
        $this->logger->info("(CLIENT API Rate() Data) ", $curlDecode);
        $this->logger->info("(CLIENT API Rate()) 4");
        if (!isset($curlDecode['error']) && !isset($curlDecode['respose']['error'])) {
            $this->logger->info("(CLIENT API Rate()) 5");
            if ($curlDecode['rate'] < 1) {
                $this->logger->info("(CLIENT API Rate()) 6");
                $this->rate = number_format($curlDecode['rate'], 8);
                $this->logger->info("(CLIENT API Rate()) 7");
            } else {
                $this->logger->info("(CLIENT API Rate()) 8");
                $this->rate = $curlDecode['rate'];
                $this->logger->info("(CLIENT API Rate()) 9");
            }
        } else {
            $this->Set_Error("rate", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Limit()
    {
        $this->logger->info("(CLIENT API Limit) 1");
        $curl = $this->xCurl();
        $this->logger->info("(CLIENT API Limit) 2");
        $limit = 'https://shapeshift.io/limit/' . $this->pair;
        $this->logger->info("(CLIENT API Limit) 3");
        $curl->get($limit, []);
        $this->logger->info("(CLIENT API Limit) 4");
        $curlDecode = json_decode($curl->getBody(), true);
        $this->logger->info("(CLIENT API Limit DATA ) ", $curlDecode);
        $this->logger->info("(CLIENT API Limit) 5");
        if (!isset($curlDecode['error']) && !isset($curlDecode['response']['error'])) {
            $this->limit = $curlDecode['limit'];
        } else {
            $this->Set_Error("limit", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Rate_Limit()
    {
        $this->logger->info("(CLIENT API Rate_Limit) 1");
        $this->Rate();
        $this->logger->info("(CLIENT API Rate_Limit) 2");
        $this->Limit();
        $this->logger->info("(CLIENT API Rate_Limit) 3");
        if ($this->status === "SUCCESS") {
            $this->logger->info("(CLIENT API Rate_Limit) 4");
            $this->logger->info("(CLIENT API Rate_Limit Rate:) " . $this->rate);
            $this->logger->info("(CLIENT API Rate_Limit Limit:) " . $this->limit);
            $this->rate_limit = bcmul($this->rate, $this->limit, 8) . " " . strtoupper($this->outputCrypto);
            $this->logger->info("(CLIENT API Rate_Limit) 5");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function RecentTx($max = 12)
    {
        $curl = $this->xCurl();
        $recenttx = 'https://shapeshift.io/recenttx/' . $max;
        $curl->get($recenttx);
        if (!$curl->error && !isset($curl->response->error)) {
            $this->recenttx = $curl->response;
        } else {
            $this->Set_Error("recenttx", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function Get_Status($response)
    {
        if ($response->status === "no_deposits") {
            $this->txStatus = "WAITING";
        } elseif ($response->status === "received") {
            $this->txStatus = "PROCESSING";
        } elseif ($response->status === "complete") {
            $this->txStatus = "COMPLETED";
            $this->txStatus->txData = $response;
            $this->txID = $response->transaction;
            if (isset($this->email)) {
                $this->Mail();
            }
        } elseif ($response->status === "failed") {
            $this->txStatus = "FAILED";
            $this->txStatus->txData = $response->error;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function TxStatus()
    {
        /*if (isset($this->depoAddress)) {
            $depoaddress = $this->depoAddress;
            $curl = $this->xCurl();
            $txStatus = 'https://shapeshift.io/txStat/' . $depoaddress;
            $curl->get($txStatus);
            if (!$curl->error && (!isset($curl->response->error) || $curl->response->status === "failed")) {
                $this->Get_Status($curl->response);
            } else {
                $this->Set_Error("txStatus", $curl);
            }
        } else {
            $this->Set_Error("txStatus", "Deposit address notset", 1);
        }*/
    }

    /**
     * {@inheritdoc}
     */
    public function TimeRemaining()
    {
        $this->logger->info("(CLIENT API) depoAddress: " . $this->depoAddress);
        $this->logger->info("(CLIENT API) amount: " . $this->amount);
        if (isset($this->depoAddress) && is_numeric($this->amount) && $this->amount > 0) {
            $this->logger->info("(CLIENT API) 1");
            $curl = $this->xCurl();
            $this->logger->info("(CLIENT API) 2");
            $timeremaining = 'https://shapeshift.io/timeremaining/' . $this->depoAddress;
            $this->logger->info("(CLIENT API) 3");
            try {
                $this->logger->info("(CLIENT API) 4");
                $curl->get($timeremaining, []);
                $this->logger->info("(CLIENT API) 5");
            } catch (\Exception $e) {
                $this->logger->info("(CLIENT API) 6");
                $this->logger->info("(CLIENT API) exception: " . $e->getMessage());
            }
            $this->logger->info("(CLIENT API) 7");
            $decodeResponse = json_decode($curl->getBody(), true);
            $this->logger->info("(CLIENT API) response status: ", $decodeResponse);
            $this->logger->info("(CLIENT API) 8");
            if (!$decodeResponse['error'] && !isset($decodeResponse['response']['error'])) {
                $this->logger->info("(CLIENT API) 9");
                if ($decodeResponse['status'] === "pending") {
                    $this->logger->info("(CLIENT API) 10");
                    $this->timeremaining = $curl->response->seconds_remaining;
                    $this->logger->info("(CLIENT API) 11");
                } elseif ($decodeResponse['status'] === "expired") {
                    $this->logger->info("(CLIENT API) 12");
                    $this->timeremaining = 0;
                    $this->logger->info("(CLIENT API) 13");
                }
                $this->logger->info("(CLIENT API) 14");
            } else {
                $this->logger->info("(CLIENT API) 15");
                $this->Set_Error("timeremaining", $curl);
                $this->logger->info("(CLIENT API) 16");
            }
            $this->logger->info("(CLIENT API) 17");

        } else {
            $this->logger->info("(CLIENT API) 18");
            $this->Set_Error("timeremaining", "Deposit address is incorrect OR amount notset", 1);
            $this->logger->info("(CLIENT API) 19");
        }
        $this->logger->info("(CLIENT API) 20");
        $this->logger->info("(CLIENT API) response status: " . $decodeResponse['status']);
        $this->logger->info("(CLIENT API) 21");
    }

    /**
     * {@inheritdoc}
     */
    private function Shift()
    {
        $this->logger->info("(CLIENT API) 1");
        $curl = $this->xCurl();
        $this->logger->info("(CLIENT API) 2");
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $this->logger->info("(CLIENT API) 3");
        $shift = 'https://shapeshift.io/shift';
        $this->logger->info("(CLIENT API) 4");
        $this->logger->info("(CLIENT API) withdrawAdd: " . $this->withdrawAdd);
        $this->logger->info("(CLIENT API) returnAddress: " . $this->returnAdd);
        $data = ["withdrawal" => $this->withdrawAdd, "pair" => $this->pair, "returnAddress" => $this->returnAdd];
        $this->logger->info("(CLIENT API) 5");
        $curlDecode = json_decode($curl->post($shift, json_encode($data)));
        $this->logger->info("(CLIENT API) 6");
        $this->logger->info("(CLIENT API) curl decode: " . $curlDecode);
        if (!$curlDecode->error && !isset($curlDecode->response->error)) {
            $this->Set_Depo_Add($curlDecode->response->deposit);
            $this->shift = $curlDecode->response;
        } else {
            $this->Set_Error("shift", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function Shift_Fixed()
    {
        $this->logger->info("(CLIENT API Shift_Fixed()) 1");
        $curl = $this->xCurl();
        $this->logger->info("(CLIENT API Shift_Fixed()) 2");
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $this->logger->info("(CLIENT API Shift_Fixed()) 3");
        $shift = 'https://shapeshift.io/sendamount';
        $this->logger->info("(CLIENT API Shift_Fixed()) 4");
        $amount = bcadd($this->amount, 0, 10);
        $this->logger->info("(CLIENT API Shift_Fixed()) 5");
        $this->logger->info("(CLIENT API Shift_Fixed() DATA SEND: withdrawal) " . $this->withdrawAdd);
        $this->logger->info("(CLIENT API Shift_Fixed() DATA SEND: returnAddress) " . $this->returnAdd);
        $data = ["amount" => $amount, "withdrawal" => $this->withdrawAdd, "pair" => $this->pair, "returnAddress" => $this->returnAdd];
        $curl->setOption(CURLOPT_POSTFIELDS, json_encode($data));
        $curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $curl->setOption(CURLOPT_TIMEOUT, 500);
        $this->logger->info("(CLIENT API Shift_Fixed()) 6");
        try {
            $curl->post($shift, []);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            $this->logger->info("(CLIENT API Shift_Fixed()) ERROR" . $e->getMessage());
            die($e->getMessage());
        }
        $this->logger->info("(CLIENT API Shift_Fixed()) 7");
        $curlDecode = json_decode($curl->getBody(), true);
        $this->logger->info("(CLIENT API Shift_Fixed() DATA)", $curlDecode);
        $this->logger->info("(CLIENT API Shift_Fixed()) 8");
        if (!isset($curlDecode['error']) && !isset($curlDecode['response']['error'])) {
            $this->logger->info("(CLIENT API Shift_Fixed()) 9");
            $this->Set_Depo_Add($curlDecode['success']['deposit']);
            $this->logger->info("(CLIENT API Shift_Fixed()) 10");
            $this->shift = $curlDecode['success'];
            $this->quotedRate = number_format($this->shift['quotedRate'], 10) . " " . strtoupper($this->outputCrypto) . " per " . strtoupper($this->inputCrypto);
        } else {
            $this->Set_Error("shift_fixed", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Run()
    {
        $this->logger->info("(CLIENT API RUN) 1");
        if ($this->Check()) {
            $this->logger->info("(CLIENT API RUN) 2");
            $this->Rate_Limit();
            $this->logger->info("(CLIENT API RUN) 3");
            if ($this->amount <= 0 && $this->status === "SUCCESS") {
                $this->logger->info("(CLIENT API RUN) 4");
                $this->Shift();
                $this->logger->info("(CLIENT API RUN) 5");
                $this->TxStatus();
                $this->logger->info("(CLIENT API RUN) 6");

                return $this;
            } elseif ($this->amount > 0 && $this->status === "SUCCESS") {
                $this->Shift_Fixed();
                /*$this->TimeRemaining();
                $this->TxStatus();*/
                return $this;
            } else {
                return FALSE;
            }
        } else {
            $this->Set_Error("go", "Invoke Setup(...) before using Go()", 1);
            return FALSE;
        }
    }

    /**
     * {@inheritdoc}
     */
    private function Mail()
    {
        if (isset($this->txID) && isset($this->email)) {
            $curl = $this->xCurl();
            $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $mail = 'https://shapeshift.io/mail';
            $data = ["email" => $this->email, "txid" => $this->txID];
            $curl->post($mail, json_encode($data));
            if (!$curl->error && !isset($curl->response->error)) {
                $this->mailer = $curl->response;
            } else {
                $this->Set_Error("mail", $curl);
            }
        } else {
            $this->Set_Error("mail", "Missing txID or email", 1);
        }
    }

    public function getAvailableCurrency($versionArray = '')
    {
        $curl = $this->xCurl();
        $currencyUrl = 'https://shapeshift.io/getcoins';
        $curl->get($currencyUrl, []);
        $curlDecode = json_decode($curl->getBody(), true);
        $arrayAvailableCurrency = [];
        foreach ($curlDecode as $k => $currency) {
            if ($versionArray == 'adminhtml') {
                $arrayAvailableCurrency[] = ['label'=>$k,'value'=>strtolower($k)];
            } else {
                $arrayAvailableCurrency[strtolower($k)] = $k;
            }
        }

        return $arrayAvailableCurrency;
    }
}