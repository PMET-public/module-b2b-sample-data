<?php
/**
 * Created by PhpStorm.
 * User: jbritts
 * Date: 10/19/16
 * Time: 10:40 AM
 */

namespace MagentoEse\B2BSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class OrderUpgrade
{
    protected $fixtureManager;
    protected $csvReader;
    protected $objectManager;
    protected $resourceConnection;
    protected $aggregateSalesReportBestsellersData;
    protected $aggregateSalesReportInvoicedData;
    protected $aggregateSalesReportOrderData;

    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Sales\Model\CronJob\AggregateSalesReportBestsellersData $aggregateSalesReportBestsellersData,
        \Magento\Sales\Model\CronJob\AggregateSalesReportInvoicedData $aggregateSalesReportInvoicedData,
        \Magento\Sales\Model\CronJob\AggregateSalesReportOrderData $aggregateSalesReportOrderData
    ) {
        try {
            $appState->setAreaCode('adminhtml');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // intentionally left empty
        }
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->resourceConnection = $resourceConnection;
        $this->aggregateSalesReportBestsellersData = $aggregateSalesReportBestsellersData;
        $this->aggregateSalesReportInvoicedData = $aggregateSalesReportInvoicedData;
        $this->aggregateSalesReportOrderData = $aggregateSalesReportOrderData;
    }

    public function setDateByOrderId(array $customerFixtures)
    {
        $dateDiff = $this->getDateDiff();
        foreach ($customerFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $ordersArray[] = array_combine($header, $row);
            }

            foreach ($ordersArray as $order){
                $this->updateOrderData($dateDiff,$order);
                $this->updateInvoiceData($dateDiff,$order);
                $this->updateShipmentData($dateDiff,$order);
            }

            unset ($ordersArray);
        }
        $this->refreshStatistics();

    }
    private function updateOrderData($dateDiff,$row){
        //sales_order,sales_order_grid
        $dateDiff = $dateDiff + $row['hours'];
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('sales_order');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR) where entity_id =".$row['entity_id'];
        $connection->query($sql);
        $tableName = $connection->getTableName('sales_order_grid');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR) where entity_id =".$row['entity_id'];
        $connection->query($sql);
        $tableName = $connection->getTableName('sales_order_item');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR) where order_id =".$row['entity_id'];
        $connection->query($sql);

    }

    private function updateInvoiceData($dateDiff,$row){
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('sales_invoice');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR) where order_id =".$row['entity_id'];
        $connection->query($sql);
        $tableName = $connection->getTableName('sales_invoice_grid');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR), order_created_at =  DATE_ADD(order_created_at,INTERVAL -".$dateDiff." HOUR) where order_id =".$row['entity_id'];
        $connection->query($sql);

    }

    private function updateShipmentData($dateDiff,$row){
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('sales_shipment');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR) where order_id =".$row['entity_id'];
        $connection->query($sql);
        $tableName = $connection->getTableName('sales_shipment_grid');
        $sql = "update " . $tableName . " set created_at =  DATE_ADD(created_at,INTERVAL -".$dateDiff." HOUR), updated_at =  DATE_ADD(updated_at,INTERVAL -".$dateDiff." HOUR), order_created_at =  DATE_ADD(order_created_at,INTERVAL -".$dateDiff." HOUR) where order_id =".$row['entity_id'];
        $connection->query($sql);

    }

    public function refreshStatistics(){
        $this->aggregateSalesReportOrderData->execute();
        $this->aggregateSalesReportBestsellersData->execute();
        $this->aggregateSalesReportInvoicedData->execute();

    }

    private function getDateDiff(){
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('sales_order');
        $sql = "select DATEDIFF(now(), max(created_at)) * 24 + EXTRACT(HOUR FROM now()) - EXTRACT(HOUR FROM max(created_at)) -1 as hours from " . $tableName;
        $result = $connection->fetchAll($sql);
        return $result[0]['hours'];
    }
}