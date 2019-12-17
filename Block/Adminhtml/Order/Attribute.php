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
namespace Ecomteck\OrderCustomAttributes\Block\Adminhtml\Order;

/**
 * Customer attributes grid container
 *
 * @author      Ecomteck <ecomteck@gmail.com>
 */
class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Define controller, block and labels
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Ecomteck_OrderCustomAttributes';
        $this->_controller = 'adminhtml_order_attribute';
        $this->_headerText = __('Customer Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
