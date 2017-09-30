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
                type     : 'shapeshift',
                component: 'Firebear_ShapeShift/js/view/payment/method-renderer/shapeshift'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);