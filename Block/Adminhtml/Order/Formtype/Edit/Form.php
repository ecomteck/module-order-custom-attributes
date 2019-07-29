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
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order\Formtype\Edit;

/**
 * Checkout Form Edit Form Block
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_themeLabelFactory = $themeLabelFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
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
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $editMode = $this->_coreRegistry->registry('edit_mode');
        if ($editMode == 'edit') {
            $saveUrl = $this->getUrl('adminhtml/*/save');
            $showNew = false;
        } else {
            $saveUrl = $this->getUrl('adminhtml/*/create');
            $showNew = true;
        }

        
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $saveUrl, 'method' => 'post']]
        );

        if ($showNew) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('General Information'), 'class' => 'fieldset-wide']
            );

            $fieldset->addField(
                'code',
                'text',
                [
                    'name' => 'code',
                    'label' => __('Form Code'),
                    'title' => __('Form Code'),
                    'required' => true,
                    'class' => 'validate-code',
                    'value' => ''
                ]
            );

            $fieldset->addField(
                'label',
                'text',
                ['name' => 'label', 'label' => __('Form Label'), 'title' => __('Form Label'), 'required' => true]
            );

            $fieldset->addField(
                'icon',
                'text',
                [
                    'name' => 'icon',
                    'label' => __('Form Icon'),
                    'title' => __('Form Icon'),
                    'required' => false
                ]
            );

            $fieldset->addField(
                'sort_order',
                'text',
                [
                    'name' => 'sort_order',
                    'label' => __('Sort Order'),
                    'title' => __('Sort Order'),
                    'value' => ''
                ]
            );

            $options = [['label' => __('Yes'), 'value' => '1'],['label' => __('No'), 'value' => '0']];
            $fieldset->addField(
                'is_step',
                'select',
                [
                    'name' => 'is_step',
                    'label' => __('Is Step'),
                    'title' => __('Is Step'),
                    'values' => $options,
                    'value' => 1
                ]
            );

            $fieldset->addField(
                'path',
                'text',
                [
                    'name' => 'path',
                    'label' => __('Component Path'),
                    'title' => __('Component Path'),
                    'value' => ''
                ]
            );
            
            $fieldset->addField(
                'is_visible',
                'select',
                [
                    'name' => 'is_visible',
                    'label' => __('Is Visible'),
                    'title' => __('Is Visible'),
                    'values' => $options,
                    'value' => 1
                ]
            );

            $form->setValues($this->_getFormType()->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
