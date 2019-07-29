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
        var ignore;

        if (config.ignore) {
            ignore = 'input[id$="full"]';
        }

        if (config.hasUserDefinedAttributes) {
            ignore = ignore ? ignore + ', ' + 'input[id$=\'_value\']' : 'input[id$=\'_value\']';
            ignore = ':hidden:not(' + ignore + ')';
        } else if (config.isDobEnabled) {
            ignore = ':hidden:not(' + ignore + ')';
        } else {
            ignore = ignore ? ':hidden:not(' + ignore + ')' : ':hidden';
        }

        return utils.extend(config, {
            ignore: ignore
        });

    };
});
