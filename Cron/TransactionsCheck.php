<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
 
namespace Firebear\ShapeShift\Cron;

use Firebear\ShapeShift\Model\ResourceModel\Transactions\CollectionFactory as TransactionsCollection;
use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;
use Magento\Sales\Model\OrderRepository;
use Firebear\ShapeShift\Model\TransactionsRepository;
use Firebear\ShapeShift\Helper\Data;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;

class TransactionsCheck
{
    private $transactionsCollectionFactory;
    private $shapeShiftClientApiFactory;
    private $transactionsRepository;
    private $orderRepository;
    private $helper;
    private $log;

    public function __construct(
        TransactionsCollection $transactionsCollectionFactory,
        ShapeShiftClientApiFactory $shapeShiftClientApiFactory,
        OrderRepository $orderRepository,
        TransactionsRepository $transactionsRepository,
        Data $helper,
        LoggerInterface $log
    ) {
        $this->transactionsCollectionFactory = $transactionsCollectionFactory;
        $this->shapeShiftClientApiFactory    = $shapeShiftClientApiFactory;
        $this->orderRepository               = $orderRepository;
        $this->transactionsRepository        = $transactionsRepository;
        $this->helper                        = $helper;
        $this->log                           = $log;
    }

    public function execute()
    {
        $transactionCollection = $this->transactionsCollectionFactory->create();
        $transactions          = $transactionCollection->addFieldToFilter('status', 1)->getItems();
        $shapeShiftClientApi   = $this->shapeShiftClientApiFactory->create();
        foreach ($transactions as $transaction) {
            $status     = $shapeShiftClientApi->getStatus($transaction->getDepositAddress());
            $orderModel = $this->orderRepository->get($transaction->getOrderId());
            if ($status == 'complete') {
                $orderModel->setState(
                    $this->helper->getGeneralConfig('status_order_paid'),
                    true
                )->setStatus($this->helper->getGeneralConfig('status_order_paid'));
                $this->orderRepository->save($orderModel);
                $transactionModel = $this->transactionsRepository->get($transaction->getId());
                $transactionModel->setStatus(2);
                $this->transactionsRepository->save($transactionModel);
            }
            if ($status == 'failed') {
                $orderModel->setState(
                    Order::STATE_CANCELED,
                    true
                )->setStatus(Order::STATE_CANCELED);
                $transactionModel = $this->transactionsRepository->get($transaction->getId());
                $this->orderRepository->save($orderModel);
                $transactionModel->setStatus(3);
                $this->transactionsRepository->save($transactionModel);
            }
            if ($status == 'no_deposits') {
                $orderModel->setState(
                    Order::STATE_PENDING_PAYMENT,
                    true
                )->setStatus(Order::STATE_PENDING_PAYMENT);
                $this->orderRepository->save($orderModel);
            }
        }
    }
}