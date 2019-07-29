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
namespace Ecomteck\OrderCustomAttributes\Controller\Adminhtml\Order;

/**
 * Adminhtml Manage Checkout Forms Controller
 */
abstract class Formtype extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\Sales\Order\FormFactory
     */
    protected $_formTypeFactory;

    /**
     * @var \Magento\Eav\Model\Form\FieldsetFactory
     */
    protected $_fieldsetFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Form\Fieldset\CollectionFactory
     */
    protected $_fieldsetsFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Form\Element\CollectionFactory
     */
    protected $_elementsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Ecomteck\OrderCustomAttributes\Model\Sales\Order\FormFactory $formTypeFactory
     * @param \Magento\Eav\Model\Form\FieldsetFactory $fieldsetFactory
     * @param \Magento\Eav\Model\ResourceModel\Form\Fieldset\CollectionFactory $fieldsetsFactory
     * @param \Magento\Eav\Model\ResourceModel\Form\Element\CollectionFactory $elementsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Ecomteck\OrderCustomAttributes\Model\Sales\Order\FormFactory $formTypeFactory,
        \Magento\Eav\Model\Form\FieldsetFactory $fieldsetFactory,
        \Magento\Eav\Model\ResourceModel\Form\Fieldset\CollectionFactory $fieldsetsFactory,
        \Magento\Eav\Model\ResourceModel\Form\Element\CollectionFactory $elementsFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_formTypeFactory = $formTypeFactory;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_fieldsetsFactory = $fieldsetsFactory;
        $this->_elementsFactory = $elementsFactory;
        parent::__construct($context);
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Ecomteck_OrderCustomAttributes::order_formtype'
        )->_addBreadcrumb(
            __('Order'),
            __('Order')
        )->_addBreadcrumb(
            __('Manage Checkout Forms'),
            __('Manage Checkout Forms')
        );
        return $this;
    }

    /**
     * Initialize and return current form type instance
     *
     * @return \Magento\Eav\Model\Form\Type
     */
    protected function _initFormType()
    {
        /** @var $model \Magento\Eav\Model\Form\Type */
        $model = $this->_formTypeFactory->create();
        $formId = $this->getRequest()->getParam('form_id');
        if (is_numeric($formId)) {
            $model->load($formId);
        }
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_form_type', $model);
        return $model;
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(null);
    }
}
