<?php

require './includes/vendor/autoload.php';
require './includes/api.lib.SurveyQueryJsonResponseBuilder.php';
require './includes/api.lib.VideoQueryJsonResponseBuilder.php';
require_once('./includes/vendor/apache/log4php/src/main/php/Logger.php');

use Aws\DynamoDb\DynamoDbClient;
use Aws\Common\Aws;

class DynamoDB {
 private $dbClient;
 private $logger;

 function __construct() {
    Logger::configure('./config/config.xml');
    $this->logger = Logger::getLogger('myLogger');
    $this->logger->trace("DynamoDB::_construct ");
    $aws = Aws::factory('./includes/api.lib.creadential.php');
    $this->dbClient = $aws->get('DynamoDb');
    #$result = $this->dbClient->listTables();
    #foreach ($result['TableNames'] as $tableName) {
    #   $this->logger->trace("DynamoDB::_construct " . $tableName );
    #}
 }

 public function putSurvey($id, $timestamp, $survey) {
     $this->logger->trace("DynamoDB::putSurvey " . $id . "  " . $timestamp . "  " . $survey );
     $result = $this->dbClient->putItem(array(
                     'TableName' => 'SurveyTable',
                        'Item' => array(
                            'BusinessId'      => array('N' => $id),
                            'Timestamp'       => array('N' => $timestamp),
                            'Survey'          => array('S' => $survey),
                         )
                     ));
 }

 public function putVideo($id, $url, $timestamp) {
     $result = $this->dbClient->putItem(array(
                     'TableName' => 'VideoTable',
                        'Item' => array(
                            'BusinessId'      => array('N' => $id),
                            'Timestamp'       => array('N' => $timestamp),
                            'Url'             => array('S' => $url)
                         )
                     ));
 }

 public function getSurvey($businessid, $time) {
    $this->logger->trace("DynamoDB::getSurvey " . $businessid . "  " . $time );
    $iterator = $this->dbClient->getIterator('Query', array(
        'TableName'     => 'SurveyTable',
        'KeyConditions' => array(
            'BusinessId' => array(
                'AttributeValueList' => array(
                    array('N' => $businessid)
                ),
                'ComparisonOperator' => 'EQ'
            ),
            'Timestamp' => array(
                'AttributeValueList' => array(
                    array('N' => strtotime("-$time minutes") - 100000000)
                ),
                'ComparisonOperator' => 'GT'
            )
        )
    ));
    $result = new SurveyQueryJsonResponseBuilder($iterator);
    $this->logger->trace("DynamoDB::getSurvey " .json_encode($result->jsonResponse));
    echo  json_encode($result->jsonResponse);
 }
 
  public function getVideo($businessid, $time) {
    $iterator = $this->dbClient->getIterator('Query', array(
        'TableName'     => 'VideoTable',
        'KeyConditions' => array(
            'BusinessId' => array(
                'AttributeValueList' => array(
                    array('N' => $businessid)
                ),
                'ComparisonOperator' => 'EQ'
            ),
            'Timestamp' => array(
                'AttributeValueList' => array(
                    array('N' => strtotime("-$time minutes") - 1000000000)
                    #array('N' => strtotime("-$time minutes"))
                ),
                'ComparisonOperator' => 'GT'
            )
        )
    ));
    $result = new VideoQueryJsonResponseBuilder($iterator);
    echo  json_encode($result->jsonResponse);
 }

 public function getBusinessIdFromName($businessName) {
    $this->logger->trace("DynamoDB::getBusinessIdFromName " . $businessName );
    $iterator = $this->dbClient->getIterator('Scan',array(
        'TableName'     => 'BusinessTable',
        'ScanFilter' => array(
            'BusinessId' => array(
                'AttributeValueList' => array(
                    array('N' => 100)
                ),
                'ComparisonOperator' => 'GT'
            )
        )
    ));
    foreach ($iterator as $item) {
      $this->logger->trace("DynamoDB::getBusinessIdFromName> " . $item['BusinessId']['N'] . "  " . $item['BusinessName']['S']);
      if( strcmp($item['BusinessName']['S'],$businessName) == 0) {
        return $item['BusinessId']['N']; 
      }
    }
    return -1;
 }

 public function putBusiness($arrayList) {
     foreach($arrayList as $item) {
      $this->dbClient->putItem($item);
     }
 }
}
?>
