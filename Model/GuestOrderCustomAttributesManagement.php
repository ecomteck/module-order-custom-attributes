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
namespace Ecomteck\OrderCustomAttributes\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestOrderCustomAttributesManagement implements \Ecomteck\OrderCustomAttributes\Api\GuestOrderCustomAttributesManagementInterface
{

    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Ecomteck\OrderCustomAttributes\Api\OrderCustomAttributesManagementInterface
     */
    protected $orderCustomAttributesManagement;
    
    /**
     * GuestOrderCustomAttributesManagement constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Ecomteck\OrderCustomAttributes\Api\OrderCustomAttributesManagementInterface $orderCustomAttributesManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Ecomteck\OrderCustomAttributes\Api\OrderCustomAttributesManagementInterface $orderCustomAttributesManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->orderCustomAttributesManagement = $orderCustomAttributesManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function saveOrderCustomAttributes(
        $cartId,
        \Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface $orderCustomAttributes
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->orderCustomAttributesManagement->saveOrderCustomAttributes($quoteIdMask->getQuoteId(), $orderCustomAttributes);
    }
}
