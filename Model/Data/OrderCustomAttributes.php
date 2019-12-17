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
namespace Ecomteck\OrderCustomAttributes\Model\Data;

use Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface;
use Magento\Framework\Api\AbstractSimpleObject;

class OrderCustomAttributes extends AbstractSimpleObject implements OrderCustomAttributesInterface
{
    const ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME = 'ecomteck_order_custom_attributes';
    
    /**
     * @return string|null
     */
    public function getOrderCustomAttributes()
    {
        return $this->_get(static::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME);
    }

    /**
     * @param string $orderCustomAttributes
     * @return $this
     */
    public function setOrderCustomAttributes($orderCustomAttributes)
    {
        return $this->setData(static::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME, $orderCustomAttributes);
    }
}
