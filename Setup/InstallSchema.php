<?php

/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */


namespace Firebear\ShapeShift\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $table = $setup->getConnection()->newTable(
            $setup->getTable('firebear_transaction_entity')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Order Id'
        )->addColumn(
            'deposit_address',
            \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Deposit address'
        )->addColumn(
            'amount_deposit',
            \Magento\Framework\Db\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Amount deposit coins to deposit address'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Status code'
        )->setComment(
            'Aitoc Dimensional Shipping order boxes'
        );
        $setup->getConnection()->createTable($table);


        $setup->endSetup();
    }
}
