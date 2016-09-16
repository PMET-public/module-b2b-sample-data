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
    protected $productsConfigure;
    protected $sharedCatalogRepository;
    protected $companyWithCatalog = 'Vandelay Industries';
    protected $sharedCatalogGroupCode = 'Tools & Lighting';
    protected $nonSharedCatalogCompany = array('Prestige Worldwide','Dunder Mifflin');

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
        \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement,
        \Magento\SharedCatalog\Model\Configure\Products $productsConfigure
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
        $this->productsConfigure = $productsConfigure;
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
        foreach($companyCustomers as $customerId){
            $cust = $this->customer->load($customerId);
            $cust->setGroupId($groupId);
            $cust->save();
         }
         //set other companies customers to the default catalog group
        $generalGroup = $this->group->load('General','customer_group_code');
        $generalGroupId = $generalGroup->getId();
        foreach($this->nonSharedCatalogCompany as $nonShared){
            $company = $this->getCompany($nonShared);
            $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
            //set customers group to shared catalog
            foreach($companyCustomers as $customerId){
                $cust = $this->customer->load($customerId);
                $cust->setGroupId($generalGroupId);
                $cust->save();
            }
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
