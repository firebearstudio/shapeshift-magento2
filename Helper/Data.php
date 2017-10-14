<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Firebear\ShapeShift\Model\CurrencyConverter\CoinMarketCapFactory;
use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;
use Magento\Framework\Math\Division;

class Data extends AbstractHelper
{
    const XML_PATH_CONFIG_COINPAYMENTS = 'payment/shape_shift/';

    private $converter;
    private $log;
    private $division;
    private $shapeShiftClientApiFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        CoinMarketCapFactory $converter,
        Division $division,
        ShapeShiftClientApiFactory $shapeShiftClientApiFactory
    ) {
        parent::__construct($context);
        $this->converter                  = $converter;
        $this->division                   = $division;
        $this->shapeShiftClientApiFactory = $shapeShiftClientApiFactory;
    }

    /**
     * @param      $field
     * @param null $storeId
     *
     * @return mixed
     */
    private function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CONFIG_COINPAYMENTS . $code, $storeId);
    }

    public function convertCurrency($amount, $currency, $selectCoin)
    {
        $shapeShiftClientApi = $this->shapeShiftClientApiFactory->create();
        $currencyName   = $shapeShiftClientApi->getCurrencyFullName($selectCoin);
        $this->_logger->info("SELECT CURRENCY NAME: ".$currencyName);
        $converterModel = $this->converter->create();
        $jsonData       = $converterModel->getCurrencyTicker(strtolower($currencyName), $currency);

        return number_format($amount / $jsonData[0]['price_usd'], 10);
    }
}
