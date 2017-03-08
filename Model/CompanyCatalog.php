<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;

//use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


class CompanyCatalog
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
    protected $management;
    protected $categoryManagement;
    protected $sharedCatalogRepository;
    protected $companyWithCatalog = 'Vandelay Industries';
    protected $sharedCatalogGroupCode = 'Tools & Lighting';
    protected $validCompanyGroupCode = 'Registered Users';
    protected $nonSharedCatalogCompanies = array('Prestige Worldwide','Dunder Mifflin');
    //protected $nonSharedCatalogCompany = 'Prestige Worldwide';
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Company\Model\CompanyRepository $companyRepository,
        \Magento\SharedCatalog\Model\SharedCatalogFactory $sharedCatalog,
        \Magento\Customer\Model\GroupFactory $customerGroup,
        \Magento\Company\Model\ResourceModel\Customer $companyCustomer,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Group $group,
        \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
        \Magento\SharedCatalog\Model\Management $management,
        \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->sharedCatalog = $sharedCatalog;
        $this->customerGroup = $customerGroup;
        $this->companyCustomer = $companyCustomer;
        $this->customer = $customer;
        $this->group = $group;
        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->management = $management;
        $this->categoryManagement = $categoryManagement;
    }

    public function install()
    {

        //get company
        $company = $this->getCompany($this->companyWithCatalog);
        //create group
        $groupAttribute = $this->customerGroup->create();
        $groupAttribute->setCode($this->sharedCatalogGroupCode);
        //$groupAttribute->set
        $groupAttribute->save();
        $groupId = $groupAttribute->getId();
        //create catalog
        $catalogData = array(
            "name" => $this->sharedCatalogGroupCode,
            "description" => "",
            "customer_group_id" => $groupId
        );
        $this->createCatalog($catalogData);
        //attach group to company
        $company->setCustomerGroupId($groupId);
        $company->save();
        //get customers attached to the company
        $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
        //set customers group to shared catalog
        foreach ($companyCustomers as $customerId) {
            $cust = $this->customer->load($customerId);
            $cust->setGroupId($groupId);
            $cust->save();
        }
        // create catalog for logged in users


        //create group
        $groupAttribute = $this->customerGroup->create();
        $groupAttribute->setCode($this->validCompanyGroupCode);
        //$groupAttribute->set
        $groupAttribute->save();
        $groupId = $groupAttribute->getId();
        //create catalog
        $catalogData = array(
            "name" => $this->validCompanyGroupCode,
            "description" => "",
            "customer_group_id" => $groupId
        );
        $this->createCatalog($catalogData);

        //get company
        foreach ($this->nonSharedCatalogCompanies as $nonSharedCatalogCompany){
            $company = $this->getCompany($nonSharedCatalogCompany);

            //attach group to company
            $company->setCustomerGroupId($groupId);
            $company->save();
            //get customers attached to the company
            $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
            //set customers group to shared catalog
            foreach ($companyCustomers as $customerId) {
                $cust = $this->customer->load($customerId);
                $cust->setGroupId($groupId);
                $cust->save();
            }
        }
        $this->__destruct();
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
    public function __destruct(){
        $this->searchCriteriaBuilder = null;
        $this->companyRepository = null;
        $this->sharedCatalog = null;
        $this->customerGroup = null;
        $this->companyCustomer = null;
        $this->customer = null;
        $this->group = null;
        $this->sharedCatalogRepository = null;
        $this->management = null;
        $this->categoryManagement = null;
    }

}
