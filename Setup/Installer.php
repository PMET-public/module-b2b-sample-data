<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\B2BSampleData\Setup;

use Magento\Framework\Setup;


class Installer implements Setup\SampleData\InstallerInterface
{

    protected $companySetup;
    protected $customerSetup;
    protected $salesrepSetup;
    protected $teamSetup;
    protected $catalogSetup;
    protected $sharedCatalogConfig;
    protected $tierPricing;



    public function __construct(
        \MagentoEse\B2BSampleData\Model\Company $companySetup,
        \MagentoEse\B2BSampleData\Model\Customer $customerSetup,
        \MagentoEse\B2BSampleData\Model\Salesrep $salesrepSetup,
        \MagentoEse\B2BSampleData\Model\Team $teamSetup,
        \MagentoEse\B2BSampleData\Model\CompanyCatalog $catalogSetup,
        \MagentoEse\B2BSampleData\Model\SharedCatalogConfig $sharedCatalogConfig,
        \MagentoEse\B2BSampleData\Model\TierPricing $tierPricing,
        \MagentoEse\B2BSampleData\Model\PreferredProducts $preferredProducts

    ) {
        $this->companySetup = $companySetup;
        $this->customerSetup = $customerSetup;
        $this->salesrepSetup = $salesrepSetup;
        $this->teamSetup = $teamSetup;
        $this->catalogSetup = $catalogSetup;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->tierPricing = $tierPricing;
        $this->preferredProducts = $preferredProducts;

    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->salesrepSetup->install(['MagentoEse_B2BSampleData::fixtures/salesreps.csv']);

        $this->customerSetup->install(['MagentoEse_B2BSampleData::fixtures/customers.csv']);

        $this->companySetup->install(['MagentoEse_B2BSampleData::fixtures/companies.csv']);

        $this->teamSetup->install(['MagentoEse_B2BSampleData::fixtures/teams.csv']);

        $this->catalogSetup->install();

        $this->tierPricing->install();

        $this->preferredProducts->install(['MagentoEse_B2BSampleData::fixtures/preferredproducts.csv']);

        //$this->sharedCatalogConfig->install();
    }
}