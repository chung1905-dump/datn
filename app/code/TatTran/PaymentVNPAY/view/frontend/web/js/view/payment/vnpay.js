define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'tattranvnpay',
                component: 'TatTran_PaymentVNPAY/js/view/payment/method-renderer/tattranvnpay'
            }
        );
        return Component.extend({});
    }
);
