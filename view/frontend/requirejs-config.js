/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            fileElement: 'Ecomteck_OrderCustomAttributes/file-element'
        }
    },
    'config': {
        'mixins': {
            'Magento_Checkout/js/model/step-navigator': {
                'Ecomteck_OrderCustomAttributes/js/view/navigator-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Ecomteck_OrderCustomAttributes/js/model/place-order-mixin': true
            },
       }
    },
};