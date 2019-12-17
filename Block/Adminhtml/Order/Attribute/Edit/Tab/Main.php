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

// @codingStandardsIgnoreFile

/**
 * Customer Attributes Edit Form
 */
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order\Attribute\Edit\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Main extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Customer data
     *
     * @var \Ecomteck\OrderCustomAttributes\Helper\Order
     */
    protected $_orderHelper;

    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory
     * @param \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker
     * @param \Ecomteck\OrderCustomAttributes\Helper\Order $orderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        \Ecomteck\OrderCustomAttributes\Helper\Order $orderHelper,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_orderHelper = $orderHelper;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $eavData,
            $yesnoFactory,
            $inputTypeFactory,
            $propertyLocker,
            $data
        );
    }

    /**
     * Preparing global layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $result = parent::_prepareLayout();
        $renderer = $this->getLayout()->getBlock('fieldset_element_renderer');
        if ($renderer instanceof \Magento\Framework\Data\Form\Element\Renderer\RendererInterface) {
            \Magento\Framework\Data\Form::setFieldsetElementRenderer($renderer);
        }
        return $result;
    }

    /**
     * Adding customer form elements for edit form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var \Magento\Customer\Model\Attribute $attribute */
        $attribute = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        /* @var $helper \Ecomteck\OrderCustomAttributes\Helper\Data */
        $helper = $this->_orderHelper;

        $fieldset->removeField('frontend_class');
        $fieldset->removeField('is_unique');

        // update Input Types
        $element = $form->getElement('frontend_input');
        $element->setValues($helper->getFrontendInputOptions());
        $element->setLabel(__('Input Type'));
        $element->setRequired(true);

        // add limitation to attribute code
        // customer attribute code can have prefix "customer_" and its length must be max length minus prefix length
        $element = $form->getElement('attribute_code');
        $oldClassName = sprintf('maximum-length-%d', \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH);
        $newClassName = sprintf(
            'maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9
        );
        $class = str_replace($oldClassName, $newClassName, $element->getClass());
        $element->setClass($class);
        $element->setNote(
            __(
                'This is used internally. Make sure you don\'t use spaces or more than %1 symbols.',
                \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH - 9
            )
        );

        $fieldset->addField('default_value_html', 'editor', [
			  'name'      => 'default_value_html',
			  'label' 	  => 'Default Value',
			  'config'    => $this->_wysiwygConfig->getConfig(),
			  'wysiwyg'   => true,
              'required'  => false,
              'value'     => $attribute->getDefaultValue()
		]);

        $fieldset->addField(
            'multiline_count',
            'text',
            [
                'name' => 'multiline_count',
                'label' => __('Lines Count'),
                'title' => __('Lines Count'),
                'required' => true,
                'class' => 'validate-digits-range digits-range-2-20',
                'note' => __('Valid range 2-20')
            ],
            'frontend_input'
        );

        $fieldset->addField(
            'input_validation',
            'select',
            [
                'name' => 'input_validation',
                'label' => __('Input Validation'),
                'title' => __('Input Validation'),
                'values' => ['' => __('None')]
            ],
            'default_value_textarea'
        );

        $fieldset->addField(
            'min_text_length',
            'text',
            [
                'name' => 'min_text_length',
                'label' => __('Minimum Text Length'),
                'title' => __('Minimum Text Length'),
                'class' => 'validate-digits'
            ],
            'input_validation'
        );

        $fieldset->addField(
            'max_text_length',
            'text',
            [
                'name' => 'max_text_length',
                'label' => __('Maximum Text Length'),
                'title' => __('Maximum Text Length'),
                'class' => 'validate-digits'
            ],
            'min_text_length'
        );

        $fieldset->addField(
            'max_file_size',
            'text',
            [
                'name' => 'max_file_size',
                'label' => __('Maximum File Size (bytes)'),
                'title' => __('Maximum File Size (bytes)'),
                'class' => 'validate-digits'
            ],
            'max_text_length'
        );

        $fieldset->addField(
            'file_extensions',
            'text',
            [
                'name' => 'file_extensions',
                'label' => __('File Extensions'),
                'title' => __('File Extensions'),
                'note' => __('Comma separated')
            ],
            'max_file_size'
        );

        $fieldset->addField(
            'max_image_width',
            'text',
            [
                'name' => 'max_image_width',
                'label' => __('Maximum Image Width (px)'),
                'title' => __('Maximum Image Width (px)'),
                'class' => 'validate-digits'
            ],
            'file_extensions'
        );

        $fieldset->addField(
            'max_image_heght',
            'text',
            [
                'name' => 'max_image_heght',
                'label' => __('Maximum Image Height (px)'),
                'title' => __('Maximum Image Height (px)'),
                'class' => 'validate-digits'
            ],
            'max_image_width'
        );

        $fieldset->addField(
            'input_filter',
            'select',
            [
                'name' => 'input_filter',
                'label' => __('Input/Output Filter'),
                'title' => __('Input/Output Filter'),
                'values' => ['' => __('None')]
            ]
        );

        $fieldset->addField(
            'date_range_min',
            'date',
            [
                'name' => 'date_range_min',
                'label' => __('Minimal value'),
                'title' => __('Minimal value'),
                'date_format' => $helper->getDateFormat()
            ],
            'default_value_date'
        );

        $fieldset->addField(
            'date_range_max',
            'date',
            [
                'name' => 'date_range_max',
                'label' => __('Maximum value'),
                'title' => __('Maximum value'),
                'date_format' => $helper->getDateFormat()
            ],
            'date_range_min'
        );

        /** @var $source \Magento\Config\Model\Config\Source\Yesno */
        $source = $this->_yesnoFactory->create();
        $yesnoSource = $source->toOptionArray();

        $fieldset->addField(
            'is_used_in_grid',
            $attribute->getBackendType() == 'static' ? 'hidden' : 'select',
            [
                'name' => 'is_used_in_grid',
                'label' => __('Add to Column Options'),
                'title' => __('Add to Column Options'),
                'values' => $yesnoSource,
                'value' => $attribute->getData('is_used_in_grid') ?: 0,
                'note' => __('Select "Yes" to add this attribute to the list of column options in the order grid.'),
            ]
        );

        $fieldset->addField(
            'is_visible_in_grid',
            'hidden',
            [
                'name' => 'is_visible_in_grid',
                'value' => $attribute->getData('is_visible_in_grid') ?: 0,
            ]
        );
        /*
        $fieldset->addField(
            'is_filterable_in_grid',
            !$attribute->getId() || $this->attributeCanBeFilterableInGrid($attribute) ? 'select' : 'hidden',
            [
                'name' => 'is_filterable_in_grid',
                'label' => __('Use in Filter Options'),
                'title' => __('Use in Filter Options'),
                'values' => $yesnoSource,
                'value' => $attribute->getData('is_filterable_in_grid') ?: 0,
                'note' => __('Select "Yes" to add this attribute to the list of filter options in the order grid.'),
            ]
        );

        $fieldset->addField(
            'is_searchable_in_grid',
            !$attribute->getId() || $this->attributeCanBeSearchableInGrid($attribute) ? 'select' : 'hidden',
            [
                'name' => 'is_searchable_in_grid',
                'label' => __('Use in Search Options'),
                'title' => __('Use in Search Option'),
                'values' => $yesnoSource,
                'value' => $attribute->getData('is_searchable_in_grid') ?: 0,
                'note' => __('Select "Yes" to add this attribute to the list of search options in the order grid.'),
            ]
        );*/

        $fieldset = $form->addFieldset('front_fieldset', ['legend' => __('Storefront Properties')]);

        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible',
                'label' => __('Show on Storefront'),
                'title' => __('Show on Storefront'),
                'values' => $yesnoSource
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'validate-digits'
            ]
        );

        $fieldset->addField(
            'checkout_step',
            'select',
            [
                'name' => 'checkout_step',
                'label' => __('Show On Checkout Step'),
                'title' => __('Show On Checkout Step'),
                'values' => $helper->getDisplayAreasInCheckoutOptions(),
                'value' => $attribute->getCheckoutStep(),
                'can_be_empty' => true
            ]
        )->setSize(
            5
        );

        if ($attribute->getId()) {
            if ($attribute->getIsSystem()) {
                foreach (['sort_order', 'is_visible', 'is_required', 'used_in_forms'] as $elementId) {
                    $form->getElement($elementId)->setDisabled(true)->setIsSystem(true);
                }
            }
            if (!$attribute->getIsUserDefined() && !$attribute->getIsSystem()) {
                foreach (['sort_order'] as $elementId) {
                    $form->getElement($elementId)->setDisabled(true);
                }
            }

            $inputTypeProp = $helper->getAttributeInputTypes($attribute->getFrontendInput());
            // input_filter
            if ($inputTypeProp['filter_types']) {
                $filterTypes = $helper->getAttributeFilterTypes();
                $values = $form->getElement('input_filter')->getValues();
                foreach ($inputTypeProp['filter_types'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_filter')->setValues($values);
            }

            // input_validation getAttributeValidateFilters
            if ($inputTypeProp['validate_filters']) {
                $filterTypes = $helper->getAttributeValidateFilters();
                $values = $form->getElement('input_validation')->getValues();
                foreach ($inputTypeProp['validate_filters'] as $filterTypeCode) {
                    $values[$filterTypeCode] = $filterTypes[$filterTypeCode];
                }
                $form->getElement('input_validation')->setValues($values);
            }
        }

        // apply scopes
        foreach ($helper->getAttributeElementScopes() as $elementId => $scope) {
            $element = $form->getElement($elementId);
            if ($element->getDisabled()) {
                continue;
            }
            $element->setScope($scope);
            if ($this->getAttributeObject()->getWebsite()->getId()) {
                $element->setName('scope_' . $element->getName());
            }
        }

        $this->getForm()->setDataObject($this->getAttributeObject());

        $this->_eventManager->dispatch(
            'ecomteck_order_custom_attributes_attribute_edit_tab_general_prepare_form',
            ['form' => $form, 'attribute' => $attribute]
        );

        return $this;
    }

    /**
     * Initialize form fileds values
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Main\AbstractMain
     */
    protected function _initFormValues()
    {
        $attribute = $this->getAttributeObject();
        if ($attribute->getId() && $attribute->getValidateRules()) {
            $this->getForm()->addValues($attribute->getValidateRules());
        }
        $result = parent::_initFormValues();

        // get data using methods to apply scope
        $formValues = $this->getAttributeObject()->getData();
        foreach (array_keys($formValues) as $idx) {
            $formValues[$idx] = $this->getAttributeObject()->getDataUsingMethod($idx);
        }
        $this->getForm()->addValues($formValues);

        return $result;
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Properties');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Properties');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function attributeCanBeFilterableInGrid($attribute) 
    {
        return in_array($attribute->getFrontendInput(), ['text', 'date', 'select', 'boolean']);
    }

    protected function attributeCanBeSearchableInGrid($attribute) 
    {
        return in_array($attribute->getFrontendInput(), ['text', 'textarea']);
    }
}
