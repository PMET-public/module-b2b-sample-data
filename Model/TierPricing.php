<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
  
 namespace MagentoEse\B2BSampleData\Model;


 class TierPricing
 {

     /**
      * @var \Magento\Framework\App\Config\ScopeConfigInterface
      */

     protected $product;
     protected $categoryManagement;



     public function __construct(

         \Magento\Catalog\Model\Product $product,
         \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement
     )
     {
         $this->product = $product;
         $this->categoryManagement = $categoryManagement;

     }

     public function install()
     {
         /* There appears to be a bug in setting tier price discount by percentage
         for the near term, the new price will be calculated and set as a new price */
         //TODO:get category by path (from config)
         //TODO:get customer group id by name
         $tierCatIds = array(44);
         $custGroup = 4;

         //TODO:get category ids by path
         //get product ids by category
         $tierProducts = array();
         foreach ($tierCatIds as $productId){
             $products = $this->categoryManagement->getCategoryProductIds($productId);
             $tierProducts = array_merge($tierProducts, $products);
         }
         foreach($tierProducts as $productId){
             $tierProduct = $this->product->load($productId);
             $orgPrice = $tierProduct->getPrice();
             $tierPriceData = array(
                 array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 10, 'price'=>round($orgPrice - ($orgPrice*.1),2)),
                 array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 20, 'price'=>round($orgPrice - ($orgPrice*.2),2))
             );
            // $foo = $this->product->getTierPrices();
             $tierProduct->setData('tier_price', $tierPriceData);
             $tierProduct->save();
         }

     }

 }
