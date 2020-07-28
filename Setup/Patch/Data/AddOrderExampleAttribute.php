<?php
/**
 * @TODO: this one does not work because an error:
 * Unable to apply data patch Namluu\Sap\Setup\Patch\Data\AddOrderExampleAttribute for module Namluu_Sap.
 * Original exception message: DDL statements are not allowed in transactions
 * Reason: Order is flat table not EAV so we can only add new column, not eav attribute
 */
namespace Namluu\Sap\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

class AddOrderExampleAttribute implements DataPatchInterface
{
    private $moduleDataSetup;

    private $salesSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function apply()
    {
        /**
         * Prepare database for install
         */
        $this->moduleDataSetup->getConnection()->startSetup();

        $setup = $this->salesSetupFactory->create();
        $setup->addAttribute(Order::ENTITY, 'example_attribute', [
            'name' => 'Example Attribute',
            'system' => false,
            'required' => false,
            'user_defined' => true,
        ]);

        /**
         * Prepare database after install
         */
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        // bypass the execution
        return '2.0.0';
    }
}
