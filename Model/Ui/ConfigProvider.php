<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ShapeShift\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Firebear\ShapeShift\Model\Client\ShapeShiftClientApiFactory;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    private $shapeShiftClientApiFactory;

    public function __construct(ShapeShiftClientApiFactory $shapeShiftClientApiFactory)
    {
        $this->shapeShiftClientApiFactory = $shapeShiftClientApiFactory;
    }

    const CODE = 'shape_shift';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $shapeShiftClientApiModel = $this->shapeShiftClientApiFactory->create();
        $configArray = [
            'payment' => [
                self::CODE => [
                    'currencyCode' => $shapeShiftClientApiModel->getAvailableCurrency(),
                    'paymentDescription' => $shapeShiftClientApiModel->getPaymentDescription()
                ]
            ]
        ];
        return $configArray;
    }
}