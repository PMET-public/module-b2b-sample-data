<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
  
 namespace MagentoEse\B2BSampleData\Model;

 use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


 class TierPricing
 {

     /**
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */
     protected $sampleDataContext;



     public function __construct(
         SampleDataContext $sampleDataContext
     )
     {
         $this->fixtureManager = $sampleDataContext->getFixtureManager();
         $this->csvReader = $sampleDataContext->getCsvReader();

     }

     public function install()
     //public function install(array $fixtures)
     {

         /*foreach ($fixtures as $fileName) {
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
                 $data['company_customers'] = explode(",", $data['company_customers']);*/



/*
             }
         }*/

     }

 }
