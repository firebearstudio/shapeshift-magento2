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

class Data extends AbstractHelper
{
    const XML_PATH_CONFIG_COINPAYMENTS = 'payment/shape_shift/';

    private $converter;
    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context, CoinMarketCapFactory $converter)
    {
        parent::__construct($context);
        $this->converter = $converter;
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

    public function convertCurrency($amount, $currency)
    {
        $converterModel = $this->converter->create();
        $jsonData = $converterModel->getCurrencyTicker('bitcoin',$currency);
        $convertedAmount = $amount / $jsonData[0]['price_usd'];
        return $convertedAmount;
    }
}
