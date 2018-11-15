<?php 
namespace CustomerLogin\Tracking\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
 
class InstallSchema implements InstallSchemaInterface {
 
    public function install( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;
        $installer->startSetup();
 
        /**
         *  Create table 'posts'
            public function addColumn($name, $type, $size = null, $options = [], $comment = null)
            string $name the column name - 1st argument.
            string $type the column data type - 2nd argument.
            string | int | array $size the column length - 3rd argument.
            array $options array of additional options - 4th argument.
            string $comment column description - 5th argument.
         */
        $connection = $installer->getConnection() ;
        $table = $connection->newTable(
            $installer->getTable( 'customer_login_history' )
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            10,
            [ 'auto_increment' => true,'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true ],
            'ID'
        )->addColumn(
            'ip_address',
            Table::TYPE_TEXT,
            128,
            [ 'nullable' => false ],
            'IP Address'
        )->addColumn(
            'user_agent',
            Table::TYPE_TEXT,
            128,
            [ 'nullable' => false ],
            'User Agent'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER, 
            10,
            [ 'nullable' => false,'unsigned' => true ],
            'Customer ID'
        )->addColumn(
            'login_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Login Time'
        )->setComment(
            'customer login history Table'
          )->addForeignKey(
                $installer->getFkName(
                    'customer_login_history',
                    'entity_id',
                    'customer_entity',
                    'customer_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'), 
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_RESTRICT
            )
            ;
        
        $connection->createTable( $table );
        $installer->endSetup();
    }
}
