<?php
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
namespace Ecomteck\OrderCustomAttributes\Api;

/**
 * Interface for saving the checkout comment to the quote for guest orders
 */
interface GuestOrderCustomAttributesManagementInterface
{
    /**
     * @param string $cartId
     * @param \Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface $orderCustomAttributes
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveOrderCustomAttributes(
        $cartId,
        \Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface $orderCustomAttributes
    );
}
