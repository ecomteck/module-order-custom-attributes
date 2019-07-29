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
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order\Formtype\Edit\Tab;

/**
 * Checkout Form Edit General Tab Block
 */
class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * Initialize Edit Form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
        parent::_construct();
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Eav\Model\Form\Type */
        $model = $this->_coreRegistry->registry('current_form_type');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('general_fieldset', ['legend' => __('General Information')]);

        $fieldset->addField('continue_edit', 'hidden', ['name' => 'continue_edit', 'value' => 0]);
        $fieldset->addField('form_id', 'hidden', ['name' => 'form_id', 'value' => $model->getId()]);

        $fieldset->addField('form_type_data', 'hidden', ['name' => 'form_type_data']);

        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('Form Code'),
                'title' => __('Form Code'),
                'required' => true,
                'class' => 'validate-code',
                'disabled' => true,
                'value' => $model->getCode()
            ]
        );

        $fieldset->addField(
            'label',
            'text',
            [
                'name' => 'label',
                'label' => __('Form Label'),
                'title' => __('Form Label'),
                'required' => true,
                'value' => $model->getLabel()
            ]
        );

        $fieldset->addField(
            'icon',
            'text',
            [
                'name' => 'icon',
                'label' => __('Form Icon'),
                'title' => __('Form Icon'),
                'value' => $model->getIcon()
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'value' => $model->getSortOrder()
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
                'value' => $model->getIsStep()
            ]
        );
        $fieldset->addField(
            'path',
            'text',
            [
                'name' => 'path',
                'label' => __('Component Path'),
                'title' => __('Component Path'),
                'value' => $model->getPath()
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
                'value' => $model->getIsVisible()
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Retrieve Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('General');
    }

    /**
     * Check is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check tab is hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
