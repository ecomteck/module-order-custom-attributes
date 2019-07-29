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
namespace Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Formtype;

class Create extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Formtype
{
    /**
     * Create new form type from skeleton
     *
     * @return void
     */
    public function execute()
    {
        $formType = $this->_initFormType();
        $redirectUrl = $this->getUrl('adminhtml/*/*');
        $data = $this->getRequest()->getPostValue();
        $data['is_system'] = 0;
        try {
            $hasError = false;
            $formType->addData($data);
            $formType->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $hasError = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $hasError = true;
            $this->messageManager->addException($e, __("We can't save the form type right now."));
        }
        if ($hasError) {
            $this->_getSession()->setFormData($this->getRequest()->getPostValue());
            $redirectUrl = $this->getUrl('adminhtml/*/new');
        } else {
            $redirectUrl = $this->getUrl('adminhtml/*/edit/', ['form_id' => $formType->getId()]);
        }

        $this->getResponse()->setRedirect($redirectUrl);
    }
}
