<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Psr\Log\LoggerInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    private $registry;
    private $logger;

    public function __construct(
        \Magento\Framework\Registry $registry,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);
        $paymentInfo = $method->getInfoInstance();
        $dataArray = $data->getDataByKey('additional_data');
        if ($data->getDataByKey('transaction_result') !== null) {
            $paymentInfo->setAdditionalInformation(
                'transaction_result',
                $dataArray['transaction_result']
            );
            $paymentInfo->setAdditionalInformation(
                'return_address',
                $dataArray['return_address']
            );
        }
    }
}
