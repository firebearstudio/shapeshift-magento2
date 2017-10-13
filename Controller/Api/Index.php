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
    private $orderRepository;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context                     $context
     * @param \Magento\Framework\App\Config\MutableScopeConfigInterface $config
     * @param \Magento\Checkout\Model\Cart                              $cart
     * @param \Magento\Quote\Model\QuoteFactory                         $quoteFactory
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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository
    ) {
        $this->config              = $config;
        $this->cart                = $cart;
        $this->quoteFactory        = $quoteFactory;
        $this->scopeConfig         = $scopeConfig;
        $this->logger              = $logger;
        $this->checkoutSession     = $checkoutSession;
        $this->shapeShiftClientApi = $shapeShiftClientApi;
        $this->shapeShiftHelper    = $shapeShiftHelper;
        $this->currency            = $currency;
        $this->storeManager        = $storeManager;
        $this->resultJsonFactory   = $resultJsonFactory;
        $this->orderRepository     = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->logger->info("API START");
        $returnAddress  = $this->getRequest()->getParam('returnAddress');
        $depositAddress = $this->shapeShiftHelper->getGeneralConfig('deposit_address');
        $currencyCode   = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $outputCrypto   = $this->shapeShiftHelper->getGeneralConfig('currency_crypto');
        $amount         = $this->shapeShiftHelper->convertCurrency(
            $this->cart->getQuote()->getGrandTotal(),
            $currencyCode,
            $outputCrypto
        );
        $this->logger->info("DEPOSIT ADDRESS: " . $depositAddress);
        $this->logger->info("RETURN ADDRESS: " . $returnAddress);
        $this->logger->info("AMOUNT: " . $amount);

        $this->logger->info("API XCHECK DEFAULT");
        $shapeShift = $this->shapeShiftClientApi->create();
        $this->logger->info("API XCHECK AMOUNT");
        $inputCrypto = $this->getRequest()->getParam('currencyCode');
        $shapeShift->sendFixedAmount($amount, $depositAddress, $returnAddress, $inputCrypto, $outputCrypto);
        $result = $this->resultJsonFactory->create();
        if (isset($shapeShift->error['error'])) {
            $jsonResponse = $shapeShift->error;
        } else {
            $jsonResponse = [
                'amount'  => $shapeShift->depoAmount,
                'address' => $shapeShift->depoAddress
            ];
        }


        return $result->setData($jsonResponse);
        $this->logger->info("API STOP");
    }
}
