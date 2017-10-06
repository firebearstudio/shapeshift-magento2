<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Controller\Api;

use Psr\Log\LoggerInterface;
use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    private $configResource;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $cart;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    private $logger;
    private $checkoutSession;
    private $shapeShiftClientApi;
    private $shapeShiftHelper;
    private $currency;
    private $storeManager;
    private $resultJsonFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\MutableScopeConfigInterface $config
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\MutableScopeConfigInterface $config,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        ShapeShiftClientApiFactory $shapeShiftClientApi,
        \Firebear\ShapeShift\Helper\Data $shapeShiftHelper,
        LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory
    )
    {
        $this->config = $config;
        $this->cart = $cart;
        $this->quoteFactory = $quoteFactory;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        $this->shapeShiftClientApi = $shapeShiftClientApi;
        $this->shapeShiftHelper = $shapeShiftHelper;
        $this->currency = $currency;
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->logger->info("API START");
        $orderId = $this->checkoutSession->getLastRealOrder()->getId();
        $returnAddress = $this->getRequest()->getParam('returnAddress');
        $depositAddress = $this->shapeShiftHelper->getGeneralConfig('deposit_address');
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $amount = $this->shapeShiftHelper->convertCurrency($this->cart->getQuote()->getGrandTotal(), $currencyCode);
        $this->logger->info("DEPOSIT ADDRESS: ".$depositAddress);
        $this->logger->info("RETURN ADDRESS: ".$returnAddress);
        $this->logger->info("AMOUNT: ".$amount);

            $this->logger->info("API XCHECK DEFAULT");
            $ShapeShift = $this->shapeShiftClientApi->create();
            if ($amount > 0) {
                $this->logger->info("API XCHECK AMOUNT");
                $ShapeShift->Setup($depositAddress, $returnAddress,"ded2.94@tut.by", $amount);
                $inputCrypto = $this->getRequest()->getParam('currencyCode');
                $outputCrypto = $this->shapeShiftHelper->getGeneralConfig('currency_crypto');
                $ShapeShift->Pairing($inputCrypto, $outputCrypto);
                $ShapeShift->Run();
                $result = $this->resultJsonFactory->create();
                return $result->setData($ShapeShift->depoAddress);
            } else {
                $ShapeShift->set_depo_Add($depositAddress);
            }
        $this->logger->info("API STOP");
    }
}
