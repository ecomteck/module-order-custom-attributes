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
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order\Formtype;

/**
 * Create New Checkout Form Block
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _getFormType()
    {
        return $this->_coreRegistry->registry('current_form_type');
    }

    /**
     * Initialize Form Container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'form_id';
        $this->_blockGroup = 'Ecomteck_OrderCustomAttributes';
        $this->_controller = 'adminhtml_order_formtype';

        parent::_construct();

        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            /*
            $this->buttonList->update('save', 'onclick', 'edit_form.save(false)');
            $this->buttonList->update('save', 'data_attribute', null);
            $this->buttonList->add(
                'save_and_edit_button',
                ['label' => __('Save and Continue Edit'), 'onclick' => 'edit_form.save(true)', 'class' => 'save']
            );*/

            if ($this->_getFormType()->getIsSystem()) {
                $this->buttonList->remove('delete');
            }

            $this->_headerText = __('Edit Checkout Form "%1"', $this->_getFormType()->getCode());
        } else {
            $this->_headerText = __('New Checkout Form');
        }
    }
}
