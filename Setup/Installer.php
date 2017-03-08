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
    protected $relatedProducts;
    protected $sampleOrder;
    protected $index;


    public function __construct(
        \MagentoEse\B2BSampleData\Model\Company $companySetup,
        \MagentoEse\B2BSampleData\Model\Customer $customerSetup,
        \MagentoEse\B2BSampleData\Model\Salesrep $salesrepSetup,
        \MagentoEse\B2BSampleData\Model\Team $teamSetup,
        \MagentoEse\B2BSampleData\Model\CompanyCatalog $catalogSetup,
        \MagentoEse\B2BSampleData\Model\SharedCatalogConfig $sharedCatalogConfig,
        \MagentoEse\B2BSampleData\Model\TierPricing $tierPricing,
        \MagentoEse\B2BSampleData\Model\PreferredProducts $preferredProducts,
        \MagentoEse\B2BSampleData\Model\Related $relatedProducts,
        \MagentoEse\SalesSampleData\Model\Order $sampleOrder,
        \Magento\Indexer\Model\Processor $index

    ) {
        $this->companySetup = $companySetup;
        $this->customerSetup = $customerSetup;
        $this->salesrepSetup = $salesrepSetup;
        $this->teamSetup = $teamSetup;
        $this->catalogSetup = $catalogSetup;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->tierPricing = $tierPricing;
        $this->preferredProducts = $preferredProducts;
        $this->relatedProducts = $relatedProducts;
        $this->sampleOrder = $sampleOrder;
        $this->index = $index;

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

        $this->relatedProducts->install(['MagentoEse_B2BSampleData::fixtures/related_products.csv']);

        //$this->index->reindexAll();

        $this->sharedCatalogConfig->install();

        $this->preferredProducts->install(['MagentoEse_B2BSampleData::fixtures/preferredproducts.csv']);

        $this->tierPricing->install();

        $this->sampleOrder->install(['MagentoEse_B2BSampleData::fixtures/orders.csv']);



    }
}