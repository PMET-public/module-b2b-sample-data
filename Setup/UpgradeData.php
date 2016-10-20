<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\B2BSampleData\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;



class UpgradeData implements UpgradeDataInterface
{
    protected $customerUpgrade;
    protected $orderUpgrade;

    public function __construct(\MagentoEse\B2BSampleData\Model\CustomerUpgrade $customerUpgrade,
        \MagentoEse\B2BSampleData\Model\OrderUpgrade $orderUpgrade)
    {
        $this->customerUpgrade = $customerUpgrade;
        $this->orderUpgrade = $orderUpgrade;

    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.0.2') < 0
        ) {
            //fix elaine's address
            $this->customerUpgrade->install(['MagentoEse_B2BSampleData::fixtures/0.0.2_customerUpdate.csv']);
            $this->orderUpgrade->setDateByOrderId(['MagentoEse_B2BSampleData::fixtures/0.0.2_orderUpdate.csv']);
        }


        $setup->endSetup();
    }
}
