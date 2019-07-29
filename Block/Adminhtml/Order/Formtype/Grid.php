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
 * Checkout Forms Grid Block
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory
     */
    protected $_formTypesFactory;

    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_themeLabelFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory $formTypesFactory
     * @param \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Form\CollectionFactory $formTypesFactory,
        \Magento\Framework\View\Design\Theme\LabelFactory $themeLabelFactory,
        array $data = []
    ) {
        $this->_formTypesFactory = $formTypesFactory;
        $this->_themeLabelFactory = $themeLabelFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize Grid Block
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('code');
        $this->setDefaultDir('asc');
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Magento\Eav\Model\ResourceModel\Form\Type\Collection */
        $collection = $this->_formTypesFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('code', ['header' => __('Code'), 'index' => 'code']);

        $this->addColumn('label', ['header' => __('Label'), 'index' => 'label']);

        
        /** @var $label \Magento\Framework\View\Design\Theme\Label */
        $label = $this->_themeLabelFactory->create();
        $design = $label->getLabelsCollection();
        array_unshift($design, ['value' => 'all', 'label' => __('All Themes')]);
        
        $this->addColumn(
            'is_step',
            [
                'header' => __('Is Step'),
                'index' => 'is_step',
                'type' => 'options',
                'options' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        $this->addColumn(
            'is_visible',
            [
                'header' => __('Visible'),
                'index' => 'is_visible',
                'type' => 'options',
                'options' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve row click URL
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/*/edit', ['form_id' => $row->getId()]);
    }
}
