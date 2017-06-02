<?php
namespace Maginx\AlsoOrderedProduct\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //START: install stuff
        //END:   install stuff
        
        //START table setup
        $table = $installer->getConnection()->newTable(
            $installer->getTable('maginx_alsoorderedproduct_alsoordered')
        )->addColumn(
            'maginx_alsoorderedproduct_alsoordered_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'product_sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [ 'nullable' => false ],
            'Product Sku'
        )->addColumn(
            'also_ordered_record',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '1M',
            [ 'nullable' => false, ],
            'Also Ordered Product Sku Record'
        );
        $installer->getConnection()->createTable($table);
        //END   table setup
        $installer->endSetup();
    }
}
