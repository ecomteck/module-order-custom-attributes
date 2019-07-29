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

class Save extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Formtype
{

    /**
     * Save form Type
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $formType = $this->_initFormType();
        $redirectUrl = $this->getUrl('adminhtml/*/index');
        if ($this->getRequest()->isPost() && $formType->getId()) {
            $request = $this->getRequest();
            try {
                $hasError = false;
                $data = $this->getRequest()->getPostValue();
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $formType->getRule()->loadPost($data);
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
            }
            if ($hasError || $request->getPost('continue_edit')) {
                $redirectUrl = $this->getUrl('adminhtml/*/edit', ['form_id' => $formType->getId()]);
            }
        }
        $this->getResponse()->setRedirect($redirectUrl);
    }
}
