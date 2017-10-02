define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component,
              rendererList) {
        'use strict';
        rendererList.push(
            {
                type     : 'shape_shift',
                component: 'Firebear_ShapeShift/js/view/payment/method-renderer/shape_shift'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);