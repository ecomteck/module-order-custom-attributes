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
 * Controller for Customer Attributes Management
 */
abstract class Attribute extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ecomteck_OrderCustomAttributes::order_attributes';

    /**
     * Customer Address Entity Type instance
     *
     * @var \Magento\Eav\Model\Entity\Type
     */
    protected $_entityType;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_attrSetFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory $attrFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\Config $eavConfig,
        \Ecomteck\OrderCustomAttributes\Model\Sales\Order\AttributeFactory $attrFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attrSetFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eavConfig = $eavConfig;
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        $this->websiteFactory = $websiteFactory;
        parent::__construct($context);
    }

    /**
     * Return Customer Address Entity Type instance
     *
     * @return \Magento\Eav\Model\Entity\Type
     */
    protected function _getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = $this->_eavConfig->getEntityType('order');
        }
        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_Backend::stores_attributes'
        )->_addBreadcrumb(
            __('Order'),
            __('Order')
        )->_addBreadcrumb(
            __('Manage Order Attributes'),
            __('Manage Order Attributes')
        );
        return $this;
    }

    /**
     * Retrieve order attribute object
     *
     * @return \Ecomteck\OrderCustomAttributes\Model\Sales\Order\Attribute
     */
    protected function _initAttribute()
    {
        /** @var $attribute \Ecomteck\OrderCustomAttributes\Model\Sales\Order\Attribute */
        $attribute = $this->_attrFactory->create();
        $website = $this->getRequest()->getParam('website') ?: $this->websiteFactory->create();
        $attribute->setWebsite($website);
        return $attribute;
    }
}
