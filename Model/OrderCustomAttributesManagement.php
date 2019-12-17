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

use Ecomteck\OrderCustomAttributes\Model\Data\OrderCustomAttributes;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\ValidatorException;

class OrderCustomAttributesManagement implements \Ecomteck\OrderCustomAttributes\Api\OrderCustomAttributesManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $cartId
     * @param \Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface $orderCustomAttributes
     * @return null|string
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveOrderCustomAttributes(
        $cartId,
        \Ecomteck\OrderCustomAttributes\Api\Data\OrderCustomAttributesInterface $orderCustomAttributes
    ) {
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }
        $orderCustomAttributes = $orderCustomAttributes->getOrderCustomAttributes();

        $this->validateOrderCustomAttributes($orderCustomAttributes);

        try {
            $quote->setData(OrderCustomAttributes::ORDER_CUSTOM_ATTRIBUTES_FIELD_NAME, strip_tags($orderCustomAttributes));
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('The order custom attributes could not be saved'));
        }

        return $orderCustomAttributes;
    }

    /**
     * @param string $orderCustomAttributes
     * @throws ValidatorException
     */
    protected function validateOrderCustomAttributes($orderCustomAttributes)
    {
        //throw new ValidatorException(__('The order custom attributes could not be saved'));
        return true;
    }
}
