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
namespace Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order;

class Form extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('ecomteck_order_form', 'form_id');
    }

    public function loadByCode($code)
    {
        $field = 'code';
        $table = $this->getMainTable();
        $connection = $this->getConnection();
        $where = $connection->quoteInto("$field = ?", $code);
        $select = $connection->select()->from($table,array('form_id'))->where($where);
        $id = $connection->fetchOne($select);
        return $id;
    }
}
