<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;

//use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


class Catalog
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $searchCriteriaBuilder;
    protected $companyRepository;
    protected $sharedCatalog;
    protected $customerGroup;
    protected $companyCustomer;
    protected $customer;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Company\Model\CompanyRepository $companyRepository,
        \Magento\SharedCatalog\Model\SharedCatalogFactory $sharedCatalog,
        \Magento\Customer\Model\GroupFactory $customerGroup,
        \Magento\Company\Model\ResourceModel\Customer $companyCustomer,
        \Magento\Customer\Model\Customer $customer
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->sharedCatalog = $sharedCatalog;
        $this->customerGroup = $customerGroup;
        $this->companyCustomer = $companyCustomer;
        $this->customer = $customer;
    }

    public function install()
    {
        //get company
        $company = $this->getCompany('Prestige Worldwide');
        //create group
        $groupAttribute = $this->customerGroup->create();
        $groupAttribute->setCode('Catalog 2');
        //$groupAttribute->set
        $groupAttribute->save();
        $groupId = $groupAttribute->getId();
        //create catalog
        $catalogData = array(
            "name" => "Catalog 3",
            "description" => "description",
            "customer_group_id" => $groupId
        );
        $catalog = $this->createCatalog($catalogData);
        //attach group to company
        $company->setCustomerGroupId($groupId);
        $company->save();
        //get customers attached to the company
        $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
        //set customers group to shared catalog
        foreach($companyCustomers as $customerId){
            $cust = $this->customer->load($customerId);
            $cust->setGroupId($groupId);
            $cust->save();
         }
    }

    private function getCompany($companyName){
        $catalogFilter = $this->searchCriteriaBuilder;
        $catalogFilter->addFilter('company_name',$companyName);
        $companyList = $this->companyRepository->getList($catalogFilter->create())->getItems();
        return reset($companyList);
    }

    private function createCatalog(array $catalogData){
        $catalog = $this->sharedCatalog->create();
        $catalog->setName($catalogData['name']);
        $catalog->setDescription($catalogData['description']);
        $catalog->setType(0); //0 is custom, 1 is public
        $catalog->setCreatedBy(1);//admin user id
        $catalog->setStoreId(0);
        $catalog->setCustomerGroupId($catalogData['customer_group_id']);
        $catalog->save();
        return $catalog;
    }

}
