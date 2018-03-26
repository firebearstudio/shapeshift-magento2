<?php

namespace Firebear\ShapeShift\Plugin\Payment\Model\Method;

use Magento\Quote\Api\Data\CartInterface;
use Psr\Log\LoggerInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use Firebear\ShapeShift\Helper\Data as ShapeShiftHelper;

class Adapter
{

    private $log;
    private $checkoutSession;
    private $shapeShiftHelper;

    public function __construct(
        LoggerInterface $log,
        CheckoutSession $checkoutSession,
        ShapeShiftHelper $shapeShiftHelper
    ) {
        $this->log              = $log;
        $this->checkoutSession  = $checkoutSession;
        $this->shapeShiftHelper = $shapeShiftHelper;
    }

    public function aroundIsAvailable(
        \Magento\Payment\Model\Method\Adapter $subject,
        callable $proceed,
        CartInterface $quote = null
    ) {
        if ($subject->getCode() == 'shape_shift') {
            $quote   = $this->checkoutSession->getQuote();
            $country = $quote->getBillingAddress()->getCountry();
            if ($this->shapeShiftHelper->getGeneralConfig('allowspecific')) {
                $allowedCountry = explode(',', $this->shapeShiftHelper->getGeneralConfig('specificcountry'));
                if (!in_array($country, $allowedCountry)) {
                    return false;
                }
            }
            if ($quote->getGrandTotal() > $this->shapeShiftHelper->getGeneralConfig('max_limit_price')
                || $quote->getGrandTotal() < $this->shapeShiftHelper->getGeneralConfig('min_limit_price')) {
                return false;
            }
        }
        $returnValue = $proceed();

        return $returnValue;
    }
}