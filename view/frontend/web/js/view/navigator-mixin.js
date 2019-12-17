/**
 * Copyright © 2017 Ecomteck. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Magento_Customer/js/model/customer'
], function ($, customer) {
    'use strict';

    /**
     * - Disallow going back to Welcome step when we're logged in.
     * - Stop jerky animations between steps by removing body animations.
     */
    return function (target) {
        target.hiddenAll = function(){
            var sortedItems = target.steps.sort(this.sortItems);
            sortedItems.forEach(function (element) {
                element.isVisible(false);
            });
        };
        target.back = function() {
            var activeIndex = 0,
                code;

            this.steps.sort(this.sortItems).forEach(function (element, index) {
                if (element.isVisible()) {
                    element.isVisible(false);
                    activeIndex = index;
                }
            });

           

            if (this.steps().length > activeIndex - 1) {
                code = this.steps()[activeIndex - 1].code;
                this.steps()[activeIndex - 1].isVisible(true);
                window.location = window.checkoutConfig.checkoutUrl + '#' + code;
                document.body.scrollTop = document.documentElement.scrollTop = 0;
            }
        };
        return target;
    };
});