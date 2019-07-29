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

class Delete extends \Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order\Formtype
{
    /**
     * Delete form type
     *
     * @return void
     */
    public function execute()
    {
        $formType = $this->_initFormType();
        if ($formType->getId()) {
            if ($formType->getIsSystem()) {
                $message = __('This system form type cannot be deleted.');
                $this->messageManager->addError($message);
            } else {
                try {
                    $formType->delete();
                    $message = __('The form type has been deleted.');
                    $this->messageManager->addSuccess($message);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $message = __('We can\'t delete the form type right now.');
                    $this->messageManager->addException($e, $message);
                }
            }
        }
        $this->_redirect('adminhtml/*/index');
    }
}
