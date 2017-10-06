<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model\Adminhtml\Source;

use Magento\Payment\Model\Method\AbstractMethod;
use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;

/**
 * Class PaymentAction
 */
class CryptoCurrency implements \Magento\Framework\Option\ArrayInterface
{
    private $shapeShiftClientApiFactory;

    public function __construct(ShapeShiftClientApiFactory $shapeShiftClientApiFactory)
    {
        $this->shapeShiftClientApiFactory = $shapeShiftClientApiFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $shapeShiftClientApiModel = $this->shapeShiftClientApiFactory->create();
        $optionArray = $shapeShiftClientApiModel->getAvailableCurrency('adminhtml');
        return $optionArray;
    }
}
