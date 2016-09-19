<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;
  



 class SharedCatalogConfig {
     protected $state;
     protected $sharedCatalogRepository;
     protected $management;
     protected $configuredProducts;
     protected $categoryFactory;
     protected $categoryManagement;
     protected $productsConfigure;

   public function __construct(
       \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
       \Magento\SharedCatalog\Model\ManagementFactory $management,
        \Magento\SharedCatalog\Model\Configure\Products $configureProducts,
       \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement,
        \Magento\SharedCatalog\Model\Configure\Products $productsConfigure,
        \Magento\Eav\Model\Attribute $attribute

   ) {

       $this->sharedCatalogRepository = $sharedCatalogRepository;
       $this->management = $management;
       $this->configuredProducts = $configureProducts;
       $this->categoryManagement = $categoryManagement;
       $this->productsConfigure = $productsConfigure;
       $this->attribute = $attribute;

   }
  
     public function install( )
     {
         $this->addProductsToSharedCatalog();
     }



     private function addProductsToSharedCatalog(){
         $attrib = $this->attribute->loadByCode('catalog_product','swatch_image');
         $attrib->save();
         $entId = $attrib->getEntityId();
         $aId = $attrib->getAttributeId();
         /* add products to public catalog */
         //TODO:get category id by path
         //get product ids by category returns array
         $allProducts = $this->categoryManagement->getCategoryProductIds(3);
         //get public catalog
         $managePublic = $this->management->create();
         $publicCatalog = $managePublic->getPublicCatalog();
         //assign to default catalog
         $foo = $this->productsConfigure->getProductSkus($allProducts);
         $managePublic->assignProductsToCatalog($publicCatalog,$foo);

         /* add products to custom catalog */
         //TODO: allow multiple catalogs via csv file
         //set category ids tools = 12 lighting = 9
         $customCatIds = array(12,9);
         //TODO:get category ids by path
         //get product ids by category
         $customProducts = array();
         foreach ($customCatIds as $customCat){
             $products = $this->categoryManagement->getCategoryProductIds($customCat);
             $customProducts = array_merge($customProducts, $products);
         }
         //TODO:Get Catalog by name
         //get catalog
         $manageCustom = $this->management->create();
         $customCatalog = $this->sharedCatalogRepository->get(2);
         //assign to catalog
         $manageCustom->assignProductsToCatalog($customCatalog,$this->productsConfigure->getProductSkus($customProducts));


     }
}
