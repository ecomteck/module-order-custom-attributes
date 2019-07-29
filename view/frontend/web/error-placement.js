/**
 * Ecomteck
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Ecomteck.com license that is
 * available through the world-wide-web at this URL:
 * https://ecomteck.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ecomteck
 * @package     Ecomteck_OrderCustomAttributes
 * @copyright   Copyright (c) 2018 Ecomteck (https://ecomteck.com/)
 * @license     https://ecomteck.com/LICENSE.txt
 */
 define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    'use strict';

    return function (config) {
        var dobElement = null,
            errorClass = null;

        if (config.hasUserDefinedAttributes || config.isDobEnabled) {
            return utils.extend(config, {

                errorPlacement: function (error, element) {
                    if (element.prop('id').search('full') !== -1) {
                        dobElement = $(element).parents('.customer-dob');
                        errorClass = error.prop('class');
                        error.insertAfter(element.parent());
                        dobElement.find('.validate-custom').addClass(errorClass)
                            .after('<div class="' + errorClass + '"></div>');
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
        }
    };
});
