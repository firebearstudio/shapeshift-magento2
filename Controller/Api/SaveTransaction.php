<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Controller\Api;

use Magento\Framework\App\Action\Context;
use Firebear\ShapeShift\Model\TransactionsRepository;
use Magento\Framework\Controller\ResultFactory;

class SaveTransaction extends \Magento\Framework\App\Action\Action
{
    private $transactionsRepository;
    private $checkoutSession;
    private $registry;

    public function __construct(
        Context $context,
        TransactionsRepository $transactionsRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->checkoutSession        = $checkoutSession;
        parent::__construct($context);
        
    }

    public function execute()
    {
        $depoAddress      = $this->getRequest()->getParam('depoAddress');
        $depoAmount       = $this->getRequest()->getParam('depoAmount');
        $transactionModel = $this->transactionsRepository->create();
        $transactionModel->setOrderId($this->checkoutSession->getLastRealOrder()->getId());
        $transactionModel->setAmountDeposit($depoAmount);
        $transactionModel->setDepositAddress($depoAddress);
        $transactionModel->setStatus(1);
        $this->transactionsRepository->save($transactionModel);
    }
}
