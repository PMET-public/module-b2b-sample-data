<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
  
 namespace MagentoEse\B2BSampleData\Model;

 use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


 class Company
 {

     /**
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */
     protected $sampleDataContext;
     protected $companyCustomer;
     protected $customer;
     protected $companyManagement;
     protected $userFactory;
     protected $structure;
     protected $structureRepository;


     public function __construct(
         SampleDataContext $sampleDataContext,
         \Magento\Company\Model\Customer\Company $companyCustomer,
         \Magento\Customer\Api\CustomerRepositoryInterface $customer,
        \Magento\Company\Model\CompanyManagement $companyManagement,
         \Magento\Company\Model\ResourceModel\Customer $customerResource,
         \Magento\User\Model\UserFactory $userFactory,
        \Magento\Company\Model\StructureFactory $structure
     )
     {
         $this->fixtureManager = $sampleDataContext->getFixtureManager();
         $this->csvReader = $sampleDataContext->getCsvReader();
         $this->companyCustomer = $companyCustomer;
         $this->customer = $customer;
         $this->companyManagement = $companyManagement;
         $this->customerResource = $customerResource;
         $this->user = $userFactory;
         $this->structure = $structure;
     }

     public function install(array $fixtures)
     {

         foreach ($fixtures as $fileName) {
             $fileName = $this->fixtureManager->getFixture($fileName);
             if (!file_exists($fileName)) {
                 continue;
             }
             $rows = $this->csvReader->getData($fileName);
             $header = array_shift($rows);
             foreach ($rows as $row) {
                 $data = [];
                 foreach ($row as $key => $value) {
                     $data[$header[$key]] = $value;
                 }
                 $data['company_customers'] = explode(",", $data['company_customers']);
                 //get customer for admin user
                 $adminCustomer = $this->customer->get($data['admin_email']);
                 //create company
                 $newCompany = $this->companyCustomer->createCompany($adminCustomer, $data);

                 if(count($data['company_customers']) > 0){
                     foreach ($data['company_customers'] as $companyCustomerEmail) {
                         //tie other customers to company
                         $companyCustomer = $this->customer->get(trim($companyCustomerEmail));
                         $this->addCustomerToCompany($newCompany,$companyCustomer);
                         /* add the customer in the tree under the admin user
                         They may be moved later on if they are part of a team */
                         $this->addToTree($companyCustomer->getId(),$adminCustomer->getId());

                     }

                 }

             }
         }

     }
     private function addCustomerToCompany($newCompany,$companyCustomer){

         //assign to company
         if ($companyCustomer->getExtensionAttributes() !== null
             && $companyCustomer->getExtensionAttributes()->getCompanyAttributes() !== null) {
             $companyAttributes = $companyCustomer->getExtensionAttributes()->getCompanyAttributes();
             $companyAttributes->setCustomerId($companyCustomer->getId());
             $companyAttributes->setCompanyId($newCompany->getId());
             $this->customerResource->saveAdvancedCustomAttributes($companyAttributes);
         }
     }
     private function addToTree($customerId,$parentId){
         $newStruct = $this->structure->create();
         $newStruct->setEntityId($customerId);
         $newStruct->setEntityType(0);
         $newStruct->setParentId($parentId);
         $newStruct->setPath('1/2');
         $newStruct->setLevel(1);
         $newStruct->save();
     }
 }
