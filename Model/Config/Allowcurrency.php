<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Firebear\ShapeShift\Model\Config;

use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;

class Allowcurrency implements \Magento\Framework\Option\ArrayInterface
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
        return $shapeShiftClientApiModel->getAvailableCurrency('adminhtml');
    }
}
