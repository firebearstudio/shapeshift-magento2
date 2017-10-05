<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Observer;

use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderRepository;

class AfterPlaceOrder implements ObserverInterface
{
    private $shapeShiftClientApi;
    private $registry;
    private $orderRepository;

    public function __construct(
        ShapeShiftClientApiFactory $shapeShiftClientApi,
        \Magento\Framework\Registry $registry,
        OrderRepository $orderRepository
    )
    {
        $this->registry = $registry;
        $this->shapeShiftClientApi = $shapeShiftClientApi;
        $this->orderRepository = $orderRepository;
    }

    public function execute(Observer $observer)
    {
        foreach ($observer->getEvent()->getOrderIds() as $orderId) {
            $orderModel = $this->orderRepository->get($orderId);
            $additionalInformation = $orderModel->getPayment()->getAdditionalInformation();
        }
    }
}
