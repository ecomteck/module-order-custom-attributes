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
namespace Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Attribute;

class Delete extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Attribute
{
    /**
     * Delete attribute action
     *
     * @return void
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId() ||
                !$attributeObject->getIsUserDefined()
            ) {
                $this->messageManager->addError(__('You cannot delete this attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
            try {
                $attributeObject->delete();
                $this->_eventManager->dispatch(
                    'ecomteck_order_custom_attributes_attribute_delete',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccess(__('You deleted the order attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('We can\'t delete the customer address attribute right now.')
                );
                $this->_redirect('adminhtml/*/edit', ['attribute_id' => $attributeId, '_current' => true]);
                return;
            }
        }

        $this->_redirect('adminhtml/*/');
        return;
    }
}
