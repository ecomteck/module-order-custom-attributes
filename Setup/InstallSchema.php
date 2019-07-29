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

namespace Ecomteck\OrderCustomAttributes\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var array The attributes backend tables definitions.
     */
    private $backendTypes = [
        'datetime' => ['value', \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME, null, [], 'Value'],
        'decimal'  => ['value', \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, '12,4', [], 'Value'],
        'int'      => ['value', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [], 'Value'],
        'text'     => ['value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Value'],
        'varchar'  => ['value', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '255', [], 'Value'],
    ];

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->createOrderRelationTable($setup);
        $this->createAttributesTables($setup);
        $this->createAdditionalAttributeTable($setup);
        $this->createFormTypeTable($setup);
        $this->modifyOrderEntityTypeTable();
    }

    /**
     * Process the Seller's EAV table creation
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup The Setup
     *
     * @throws \Zend_Db_Exception
     */
    private function createOrderRelationTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'ecomteck_order_custom_attributes_sales_flat_order'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecomteck_order_custom_attributes_sales_flat_order')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'ecomteck_order_custom_attributes_sales_flat_order',
                'entity_id',
                'sales_order',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('sales_order'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            ' Order Sales Flat Order'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'ecomteck_order_custom_attributes_sales_flat_quote'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecomteck_order_custom_attributes_sales_flat_quote')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
            'Entity Id'
        )->addForeignKey(
            $setup->getFkName(
                'ecomteck_order_custom_attributes_sales_flat_quote',
                'entity_id',
                'quote',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('quote'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            ' Customer Sales Flat Quote'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Process the Seller's EAV Attributes tables creation
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup The Setup
     *
     * @throws \Zend_Db_Exception
     */
    private function createAttributesTables(SchemaSetupInterface $setup)
    {
        foreach ($this->backendTypes as $backendType => $valueFieldProperties) {
            $backendTableName = 'ecomteck_order_entity_' . $backendType;
            $table = $setup->getConnection()
                ->newTable($setup->getTable($backendTableName))
                ->addColumn(
                    'value_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Value ID'
                )
                ->addColumn(
                    'attribute_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Attribute ID'
                )
                ->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Store ID'
                )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Entity ID'
                );

            call_user_func_array([$table, 'addColumn'], $valueFieldProperties);

            $table->addIndex(
                $setup->getIdxName(
                    $backendTableName,
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addIndex($setup->getIdxName($backendTableName, ['entity_id']), ['entity_id'])
            ->addIndex($setup->getIdxName($backendTableName, ['attribute_id']), ['attribute_id'])
            ->addIndex($setup->getIdxName($backendTableName, ['store_id']), ['store_id'])
            ->addForeignKey(
                $setup->getFkName($backendTableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName($backendTableName, 'entity_id', 'ecomteck_order_entity_', 'entity_id'),
                'entity_id',
                $setup->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName($backendTableName, 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Ecomteck Order ' . ucfirst($backendType) . 'Attribute Backend Table');

            $setup->getConnection()->createTable($table);
        }
    }

    private function createAdditionalAttributeTable($setup)
    {
        /**
         * Create table 'ecomteck_order_eav_attribute'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecomteck_order_eav_attribute')
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => false, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addColumn(
            'is_visible',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Visible'
        )->addColumn(
            'input_filter',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Input Filter'
        )->addColumn(
            'multiline_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Multiline Count'
        )->addColumn(
            'validate_rules',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Validate Rules'
        )->addColumn(
            'is_system',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is System'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order'
        )->addColumn(
            'data_model',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Data Model'
        )->addColumn(
            'checkout_step',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Checkout Step'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'is_used_in_grid',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ],
            'Is Used in Grid'
        )->addColumn(
            'is_visible_in_grid',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ],
            'Is Visible in Grid'
        )->addColumn(
            'is_filterable_in_grid',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ],
            'Is Filterable in Grid'
        )->addColumn(
            'is_filterable_in_grid',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ],
            'Is Searchable in Grid'
        )->addForeignKey(
            $setup->getFkName('ecomteck_order_eav_attribute', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Eav Attribute'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'ecomteck_order_eav_attribute_website'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecomteck_order_eav_attribute_website')
        )->addColumn(
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Attribute Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addColumn(
            'is_visible',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Is Visible'
        )->addColumn(
            'is_required',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Is Required'
        )->addColumn(
            'default_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Default Value'
        )->addColumn(
            'multiline_count',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Multiline Count'
        )->addIndex(
            $setup->getIdxName('ecomteck_order_eav_attribute_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $setup->getFkName('ecomteck_order_eav_attribute_website', 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('ecomteck_order_eav_attribute_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $setup->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Eav Attribute Website'
        );
        $setup->getConnection()->createTable($table);
    }

    private function createFormTypeTable($setup)
    {
        /**
         * Create table 'ecomteck_order_eav_attribute_website'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('ecomteck_order_form')
        )->addColumn(
            'form_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Form Id'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Form Label'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Form Code'
        )->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Path'
        )->addColumn(
            'is_step',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Is Step'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'is_visible',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Is Visible'
        )->addColumn(
            'icon',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            [],
            'Icon'
        )->addColumn(
            'sort_order',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Sort Order'
        )->setComment(
            'Checkout Form'
        );
        $setup->getConnection()->createTable($table);
    }

    private function modifyOrderEntityTypeTable()
    {
        //Get Object Manager Instance
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $orderEntityType = $objectManager->create('Magento\Eav\Model\Entity\Type')->loadByCode('order');
        if($orderEntityType && $orderEntityType->getEntityTypeId()){
            $orderEntityType->setAttributeModel('Ecomteck\OrderCustomAttributes\Model\Sales\Order\Attribute')
                            ->setEntityAttributeCollection('Ecomteck\OrderCustomAttributes\Model\ResourceModel\Sales\Order\Attribute\Collection')
                            ->setAdditionalAttributeTable('ecomteck_order_eav_attribute')
                            ->save();
        }  
    }
}
