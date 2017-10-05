<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model\Client;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Setup\Exception;
use Psr\Log\LoggerInterface;

class ShapeShiftClientApi implements \Firebear\ShapeShift\Api\ShapeShiftClientApiInterface
{
    const constInputCrypro = 'ltc';
    const constOutputCrypto = 'btc';

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
    private $depoAddress;

    public function __construct(Curl $curl, LoggerInterface $logger)
    {
        $this->curl = $curl;
        $this->Pairing(self::constInputCrypro, self::constOutputCrypto);
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
        $this->status = "ERROR";
        $this->$error = 0;
        $erx = $error . "msg";
        if (isset($curl->error) && $shift === 0) {
            $this->$erx = "Error " . $curl->error_code . ":" . $curl->error_message;
        } elseif (isset($curl->response->error) && $shift === 0) {
            $this->$erx = "Error : " . $curl->response->error;
        } else {
            $this->$erx = "Shapeshift Error : " . $curl;
        }
        $this->logger->info($this->$erx);
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
    private function Pairing($inputCrypto, $outputCrypto)
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
        if (filter_var($this->xInput($email), FILTER_VALIDATE_EMAIL)) {
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
        $curl = $this->xCurl();
        $rate = 'https://shapeshift.io/rate/' . $this->pair;
        $curl->get($rate);
        if (!$curl->error && !isset($curl->response->error)) {
            if ($curl->response->rate < 1) {
                $this->rate = number_format($curl->response->rate, 8);
            } else {
                $this->rate = $curl->response->rate;
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
        $curl = $this->xCurl();
        $limit = 'https://shapeshift.io/limit/' . $this->pair;
        $curl->get($limit);
        if (!$curl->error && !isset($curl->response->error)) {
            $this->limit = $curl->response->limit;
        } else {
            $this->Set_Error("limit", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Rate_Limit()
    {
        $this->Rate();
        $this->Limit();
        if ($this->status === "SUCCESS") {
            $this->rate_limit = bcmul($this->rate, $this->limit, 8) . " " . strtoupper($this->output_crypto);
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
        if (isset($this->depoAddress)) {
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
        }
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
                $curl->get($timeremaining,[]);
                $this->logger->info("(CLIENT API) 5");
            } catch (\Exception $e) {
                $this->logger->info("(CLIENT API) 6");
                $this->logger->info("(CLIENT API) exception: " . $e->getMessage());
            }
            $this->logger->info("(CLIENT API) 7");
                $this->logger->info("(CLIENT API) response status: " . $curl->getBody());
                $decodeResponse = json_decode($curl->getBody(), true);
            $this->logger->info("(CLIENT API) 8");
                if (!$curl->{'error'} && !isset($curl->{'response'}->{'error'})) {
                    $this->logger->info("(CLIENT API) 9");
                    if ($curl->response->status === "pending") {
                        $this->logger->info("(CLIENT API) 10");
                        $this->timeremaining = $curl->response->seconds_remaining;
                        $this->logger->info("(CLIENT API) 11");
                    } elseif ($curl->response->status === "expired") {
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
        $this->logger->info("(CLIENT API) response status: " . $curl->response->status);
        $this->logger->info("(CLIENT API) 21");
    }

    /**
     * {@inheritdoc}
     */
    private function Shift()
    {
        $curl = $this->xCurl();
        $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $shift = 'https://shapeshift.io/shift';
        $data = ["withdrawal" => $this->withdrawAdd, "pair" => $this->pair, "returnAddress" => $this->returnAdd];
        $curl->post($shift, json_encode($data));
        if (!$curl->error && !isset($curl->response->error)) {
            $this->Set_Depo_Add($curl->response->deposit);
            $this->shift = $curl->response;
        } else {
            $this->Set_Error("shift", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function Shift_Fixed()
    {
        $curl = $this->xCurl();
        $curl->setopt(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $shift = 'https://shapeshift.io/sendamount';
        $amount = bcadd($this->amount, 0, 10);
        $data = ["amount" => $amount, "withdrawal" => $this->withdrawAdd, "pair" => $this->pair, "returnAddress" => $this->returnAdd];
        $curl->post($shift, json_encode($data));
        if (!$curl->error && !isset($curl->response->error)) {
            $this->Set_Depo_Add($curl->response->success->deposit);
            $this->shift = $curl->response->success;
            $this->shift->quotedRate = number_format($this->shift->quotedRate, 10) . " " . strtoupper($this->output_crypto) . " per " . strtoupper($this->input_crypto);
        } else {
            $this->Set_Error("shift_fixed", $curl);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Run()
    {
        if ($this->Check()) {
            $this->Rate_Limit();
            if ($this->amount <= 0 && $this->status === "SUCCESS") {
                $this->Shift();
                $this->TxStatus();
                return $this;
            } elseif ($this->amount > 0 && $this->status === "SUCCESS") {
                $this->Shift_Fixed();
                $this->TimeRemaining();
                $this->TxStatus();
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
}