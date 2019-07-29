/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_CheckoutAgreements/js/model/agreements-assigner',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'uiRegistry'
], function (
    $, 
    wrapper, 
    agreementsAssigner,
    quote,
    customer,
    urlBuilder, 
    urlFormatter, 
    errorProcessor,
    registry
) {
    'use strict';

    return function (placeOrderAction) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            agreementsAssigner(paymentData);
            var isCustomer = customer.isLoggedIn();
            var quoteId = quote.getQuoteId();

            var url;
            if (isCustomer) {
                url = urlBuilder.createUrl('/carts/mine/set-order-custom-attributes', {})
            } else {
                url = urlBuilder.createUrl('/guest-carts/:cartId/set-order-custom-attributes', {cartId: quoteId});
            }

            var shippingAddress = registry.get('checkout.steps.shipping-step.shippingAddress');
            if(shippingAddress && shippingAddress.source){
                var orderCustomAttributes = JSON.stringify(shippingAddress.source.order);
            
                var payload = {
                    cartId: quoteId,
                    orderCustomAttributes: {
                        order_custom_attributes: orderCustomAttributes
                    }
                };

                if (!payload.orderCustomAttributes.order_custom_attributes) {
                    return true;
                }

                var result = true;

                $.ajax({
                    url: urlFormatter.build(url),
                    data: JSON.stringify(payload),
                    global: false,
                    contentType: 'application/json',
                    type: 'PUT',
                    async: false
                }).done(
                    function (response) {
                        result = true;
                    }
                ).fail(
                    function (response) {
                        result = false;
                        errorProcessor.process(response);
                    }
                );
            }
            
            return originalAction(paymentData, messageContainer);
        });
    };
});