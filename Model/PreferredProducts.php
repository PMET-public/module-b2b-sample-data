<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


class PreferredProducts
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $sampleDataContext;
    protected $product;




    public function __construct(
        SampleDataContext $sampleDataContext,
       \Magento\Catalog\Model\ProductFactory $product
    )
    {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->product = $product;

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
                //set tier prices
                $tierProduct = $this->product->create();
                $tierProduct->load($tierProduct->getIdBySku($data['sku']));
                $orgPrice = $tierProduct->getPrice();
                $tierPriceData = array(
                   // array ('website_id'=>0, 'cust_group'=>0, 'price_qty' => 10, 'price'=>round($orgPrice - ($orgPrice*.1),2)),
                   // array ('website_id'=>0,'cust_group'=>0, 'price_qty' => 20, 'price'=>round($orgPrice - ($orgPrice*.2),2)),
                    array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 10, 'price'=>round($orgPrice - ($orgPrice*.1),2)),
                    array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 20, 'price'=>round($orgPrice - ($orgPrice*.2),2)),
                    array ('website_id'=>0, 'cust_group'=>5, 'price_qty' => 10, 'price'=>round($orgPrice - ($orgPrice*.1),2)),
                    array ('website_id'=>0, 'cust_group'=>5, 'price_qty' => 20, 'price'=>round($orgPrice - ($orgPrice*.2),2))
                );
                $tierProduct->setData('tier_price', $tierPriceData);
                $tierProduct->save();

            }

        }

    }

}
