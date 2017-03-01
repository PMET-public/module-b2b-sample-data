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
    protected $validCatalogName = 'Registered Users';
    protected $customCats = array('All Products/Lighting','All Products/Tools');
    protected $publicCats = array('All Products');

    public function __construct(
        \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
        \Magento\SharedCatalog\Model\ManagementFactory $management,
        \Magento\SharedCatalog\Api\ProductManagementInterface $productManagement,
       /* \Magento\SharedCatalog\Model\Configure\Products $productsConfigure,*/
        \Magento\Eav\Model\Attribute $attribute,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\SharedCatalog\Model\SharedCatalogAssignment $sharedCatalogAssignment
    )
    {


        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->management = $management;
        $this->productManagement = $productManagement;
        //$this->productsConfigure = $productsConfigure;
        $this->attribute = $attribute;
        $this->eavConfig = $eavConfig;
        $this->categoryCollection = $categoryCollection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->sharedCatalogAssignment = $sharedCatalogAssignment;

    }

    public function install(){
        /* add products to custom catalog */
        $this->assignProductsToCatalog($this->sharedCatalogName, $this->customCats);
        /* add products to registered user catalog */
        $this->assignProductsToCatalog($this->validCatalogName, $this->publicCats);
       //  $customCatIds = array();

       // foreach ($this->customCats as $categoryPath){
       //     array_push($customCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
       // }

        //get catalog id
       // $catalogId = $this->getCatalogByName($this->sharedCatalogName)->getid();
        //assign to catalog
       // $this->sharedCatalogAssignment->assignProductsForCategories($catalogId,$customCatIds);
        //assign to pubic catalog
        //$publicCatlogId = $this->
        //get product ids by category returns array
        /*foreach ($publicCats as $categoryPath){
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

        //get registered User catalog
        $catalog = $this->getCatalogByName($this->validCatalogName);
        $manageValid = $this->management->create();
        $validCatalog = $this->sharedCatalogRepository->get($catalog->getId());
        //assign to default catalog
        //$publicSkus = $this->productsConfigure->getProductSkus($publicProducts);
        try {
            $manageValid->assignProductsToCatalog($validCatalog, $publicSkus);
        }catch(\InvalidArgumentException $e){
            //Ignore the error...indexer not found, but not an issue for this operation
            $catch=0;
        }*/




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
    private function assignProductsToCatalog($catalogName, array $categoryPaths)
    {
        $customCatIds = array();
        foreach ($categoryPaths as $categoryPath){
            array_push($customCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
        }
        //get catalog id
        $catalogId = $this->getCatalogByName($catalogName)->getid();
        //assign to catalog
        $this->sharedCatalogAssignment->assignProductsForCategories($catalogId,$customCatIds);
    }
}

