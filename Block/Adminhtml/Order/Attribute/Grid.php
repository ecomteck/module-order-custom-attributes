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
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order\Attribute;

/**
 * Customer Attributes Grid Block
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
{
    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Attribute\CollectionFactory
     */
    protected $_attributesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Attribute\CollectionFactory $attributesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Attribute\CollectionFactory $attributesFactory,
        array $data = []
    ) {
        $this->_attributesFactory = $attributesFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid, set grid Id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('orderAttributeGrid');
        $this->setDefaultSort('sort_order');
    }

    /**
     * Prepare order attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Attribute\Collection */
        $collection = $this->_attributesFactory->create();
        $collection->addSystemHiddenFilter()->addExcludeHiddenFrontendFilter();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    /**
     * Prepare order attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'is_visible',
            [
                'header' => __('Visible to Customer'),
                'sortable' => true,
                'index' => 'is_visible',
                'type' => 'options',
                'options' => ['0' => __('No'), '1' => __('Yes')]
            ]
        );

        $this->addColumn(
            'sort_order',
            ['header' => __('Sort Order'), 'sortable' => true, 'index' => 'sort_order']
        );

        return $this;
    }
}
