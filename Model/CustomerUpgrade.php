<?php
/**
 * Created by PhpStorm.
 * User: jbritts
 * Date: 10/19/16
 * Time: 10:40 AM
 */

namespace MagentoEse\B2BSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class CustomerUpgrade
{
    protected $fixtureManager;
    protected $csvReader;
    protected $objectManager;

    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $appState
    ) {
        try {
            $appState->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->objectManager=$objectManager;
    }

    public function install(array $customerFixtures)
    {

        foreach ($customerFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $_productsArray[] = array_combine($header, $row);
            }
            $this->importerModel = $this->objectManager->create('FireGento\FastSimpleImport2\Model\Importer');
            $this->importerModel->setEntityCode('customer_composite');
            $this->importerModel->setValidationStrategy('validation-skip-errors');
            try {
                $this->importerModel->processImport($_productsArray);
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }

            print_r($this->importerModel->getLogTrace());
            print_r($this->importerModel->getErrorMessages());
            unset ($_productsArray);
        }

    }
}