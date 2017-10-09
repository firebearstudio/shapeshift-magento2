<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Controller\Page;

use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;

class Success extends \Magento\Framework\App\Action\Action
{
    protected $pageFactory;
    private $checkoutSession;
    private $registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->pageFactory     = $pageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->registry        = $coreRegistry;

        return parent::__construct($context);
    }

    public function execute()
    {
        $this->registry->register('last_success_order_id', $this->checkoutSession->getLastRealOrder()->getId());

        return $this->pageFactory->create();
    }
}