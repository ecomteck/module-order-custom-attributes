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

class Edit extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Attribute
{
    /**
     * Edit attribute action
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        /* @var $attributeObject \Magento\Customer\Model\Attribute */
        $attributeId = $this->getRequest()->getParam('attribute_id');
        $attributeObject = $this->_initAttribute()->setEntityTypeId($this->_getEntityType()->getId());

        if ($attributeId) {
            $attributeObject->load($attributeId);
            if (!$attributeObject->getId()) {
                $this->messageManager->addError(__('The attribute no longer exists.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
            if ($attributeObject->getEntityTypeId() != $this->_getEntityType()->getId()) {
                $this->messageManager->addError(__('You cannot edit this attribute.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
        }

        $attributeData = $this->_getSession()->getAttributeData(true);
        if (!empty($attributeData)) {
            $attributeObject->setData($attributeData);
        }
        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label = $attributeObject->getId() ? __('Edit Order Attribute') : __('New Order Attribute');

        $this->_initAction()->_addBreadcrumb($label, $label);
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Order Custom Attributes'));
        $attributeId ? $this->_view->getPage()->getConfig()->getTitle()->prepend($attributeObject->getFrontendLabel())
            : $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Order Attribute'));
        $this->_view->renderLayout();
    }
}
