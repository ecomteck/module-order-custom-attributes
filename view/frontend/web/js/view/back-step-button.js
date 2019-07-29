/**
 * Copyright © 2017 Ecomteck. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'uiComponent',
        'jquery',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function (Component, $, stepNavigator) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Ecomteck_OrderCustomAttributes/back-step-button'
            },
            initialize: function () {
            
                return this._super();
            },
            backStep: function() {
                stepNavigator.back();
            }
        });
    }
);