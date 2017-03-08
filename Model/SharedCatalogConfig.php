<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;

class SharedCatalogConfig {

    protected $sharedCatalogRepository;
    protected $categoryCollection;
    protected $searchCriteriaBuilder;
    protected $sharedCatalogName = 'Tools & Lighting';
    protected $validCatalogName = 'Registered Users';
    protected $publicCatalogName = 'Default (General)';
    protected $customCats = array('All Products/Lighting','All Products/Tools');
    protected $publicCats = array('All Products');

    public function __construct(
        \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\SharedCatalog\Model\SharedCatalogAssignment $sharedCatalogAssignment
    )
    {
        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->categoryCollection = $categoryCollection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sharedCatalogAssignment = $sharedCatalogAssignment;
    }

    public function install(){
        /* add products to custom catalog */
        $this->assignProductsToCatalog($this->sharedCatalogName, $this->customCats);
        /* add products to registered user catalog */
        $this->assignProductsToCatalog($this->validCatalogName, $this->publicCats);
        /* add products to default catalog */
        $this->assignProductsToCatalog($this->publicCatalogName, $this->publicCats);
        $this->__destruct();
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
    public function __destruct(){
        $this->sharedCatalogRepository = null;
        $this->categoryCollection = null;
        $this->searchCriteriaBuilder = null;
        $this->sharedCatalogAssignment = null;

    }
}