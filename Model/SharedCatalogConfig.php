<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;




class SharedCatalogConfig {

    protected $sharedCatalogRepository;
    protected $management;
    protected $configuredProducts;
    protected $categoryManagement;
    protected $productsConfigure;
    protected $attribute;
    protected $eavConfig;
    protected $categoryCollection;
    protected $searchCriteriaBuilder;
    protected $sharedCatalogName = 'Tools & Lighting';

    public function __construct(
        \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
        \Magento\SharedCatalog\Model\ManagementFactory $management,
        \Magento\SharedCatalog\Model\Configure\Products $configureProducts,
        \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement,
        \Magento\SharedCatalog\Model\Configure\Products $productsConfigure,
        \Magento\Eav\Model\Attribute $attribute,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {


        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->management = $management;
        $this->configuredProducts = $configureProducts;
        $this->categoryManagement = $categoryManagement;
        $this->productsConfigure = $productsConfigure;
        $this->attribute = $attribute;
        $this->eavConfig = $eavConfig;
        $this->categoryCollection = $categoryCollection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

    }

    public function install(){
        /* add products to custom catalog */
        $customCats = array('All Products/Lighting','All Products/Tools');
        $publicCats = array('All Products');
        //TODO: allow multiple catalogs via csv file
        //set category ids tools = 13 lighting = 10
        //$customCatIds = array(13,10);
        $publicCatIds = array();
        $customCatIds = array();

        foreach ($customCats as $categoryPath){
            array_push($customCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
        }
        //get product ids by category
        $customProducts = array();
        foreach ($customCatIds as $customCat){
            $products = $this->categoryManagement->getCategoryProductIds($customCat);
            $customProducts = array_merge($customProducts, $products);
        }
        //get catalog
        $catalog = $this->getCatalogByName($this->sharedCatalogName);
        $manageCustom = $this->management->create();
        $customCatalog = $this->sharedCatalogRepository->get($catalog->getId());
        //assign to catalog
        try {
            $manageCustom->assignProductsToCatalog($customCatalog,$this->productsConfigure->getProductSkus($customProducts));
        }catch(\InvalidArgumentException $e){
            //Ignore the error...indexer not found, but not an issue for this operation
            $catch=0;
        }

        //get product ids by category returns array
        foreach ($publicCats as $categoryPath){
            array_push($publicCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
        }
        $publicProducts = array();
        foreach ($publicCatIds as $publicCat){
            $products = $this->categoryManagement->getCategoryProductIds($publicCat);
            $publicProducts = array_merge($publicProducts, $products);
        }
        //$allProducts = $this->categoryManagement->getCategoryProductIds(3);
        //get public catalog
        $managePublic = $this->management->create();
        $publicCatalog = $managePublic->getPublicCatalog();
        $publicCatalog->setCustomerGroupId(0);
        //assign to default catalog
        $publicSkus = $this->productsConfigure->getProductSkus($publicProducts);
        try {
            $managePublic->assignProductsToCatalog($publicCatalog, $publicSkus);
        }catch(\InvalidArgumentException $e){
            //Ignore the error...indexer not found, but not an issue for this operation
            $catch=0;
        }



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

    protected function getCatalogByName($catalogName){
        $catalogFilter = $this->searchCriteriaBuilder;
        $catalogFilter->addFilter('name',$catalogName);
        $catalogList = $this->sharedCatalogRepository->getList($catalogFilter->create())->getItems();
        return reset($catalogList);
    }
}

