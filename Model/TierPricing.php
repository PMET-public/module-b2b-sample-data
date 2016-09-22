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
    protected $categoryCollection;
    protected $group;



    public function __construct(

        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Customer\Model\Group $group
    )
    {
        $this->product = $product;
        $this->categoryManagement = $categoryManagement;
        $this->categoryCollection = $categoryCollection;
        $this->group = $group;

    }

    public function install()
    {
        /* There appears to be a bug in setting tier price discount by percentage
        for the near term, the new price will be calculated and set as a new price */
        //TODO:get category from config
        $categoryPaths = array('All Products/Lighting/LED Lamps');
        $tierCatIds = array();

        foreach ($categoryPaths as $categoryPath){
            array_push($tierCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
        }


        $custGroup = $this->getGroupIdFromName('Tools & Lighting');

        //get product ids by category
        $tierProducts = array();
        foreach ($tierCatIds as $productId){
            $products = $this->categoryManagement->getCategoryProductIds($productId);
            $tierProducts = array_merge($tierProducts, $products);
        }
        foreach($tierProducts as $productId){
            $tierProduct = $this->product->create();
            $tierProduct->load($productId);
            $orgPrice = $tierProduct->getPrice();
            $tierPriceData = array(
                array ('website_id'=>0, 'cust_group'=>$custGroup, 'price_qty' => 10, 'price'=>round($orgPrice - ($orgPrice*.1),2)),
                array ('website_id'=>0, 'cust_group'=>$custGroup, 'price_qty' => 20, 'price'=>round($orgPrice - ($orgPrice*.2),2))
            );
            // $foo = $this->product->getTierPrices();
            $tierProduct->setData('tier_price', $tierPriceData);
            $tierProduct->save();

        }

    }

    protected function getGroupIdFromName($name){
        $groupLoad = $this->group->load($name,'customer_group_code');
        return $groupLoad->getid();
    }

    protected function getIdFromPath($categories,$string)
    {
        if (in_array($string, array_keys($categories))) {
            return $categories[$string];
        }

        return false;
    }

    protected function _initCategories()
    {
        $collection = $this->categoryCollection->addNameToResult();
        $categories = array();
        $categoriesWithRoots = array();
        foreach ($collection as $category) {
            $structure = explode('/', $category->getPath());
            $pathSize = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                $rootCategoryName = array_shift($path);
                if (!isset($categoriesWithRoots[$rootCategoryName])) {
                    $categoriesWithRoots[$rootCategoryName] = array();
                }
                $index = implode('/', $path);
                $categoriesWithRoots[$rootCategoryName][$index] = $category->getId();
                if ($pathSize > 2) {
                    $categories[$index] = $category->getId();
                }
            }
        }
        return $categories;
    }


}
