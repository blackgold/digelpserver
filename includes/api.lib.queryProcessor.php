<?php
require './includes/vendor/autoload.php';
#require './includes/api.lib.dynamodb.php';
require_once('./includes/vendor/apache/log4php/src/main/php/Logger.php');

class QueryProcessor {
 private $db;
 private $query;
 private $queryArray;
 private $logger;

 function __construct() {
    Logger::configure('./config/config.xml');
    $this->logger = Logger::getLogger('myLogger');
    $this->logger->trace("QueryProcessor::_construct ");
 }

 public function processSurveyQuery($query) {
   $this->logger->trace("QueryProcessor::processSurveyQuery " . $query); 
   $this->query = $query;
   parse_str($this->query); 
   if($BusinessName) {
       $this->logger->trace("QueryProcessor::processSurveyQuery " . $BusinessName); 
       $db = new DynamoDB();
       $businessId = $db->getBusinessIdFromName($BusinessName);
       $this->logger->trace("QueryProcessor::processSurveyQuery " . $businessId);
       $db->getSurvey($businessId,0);
   }
 }

 public function processVideoQuery($query) {
   $this->logger->trace("QueryProcessor::processVideoQuery " . $query);
   $this->query = $query;
   parse_str($this->query);
   if($BusinessName) {
       $this->logger->trace("QueryProcessor::processVideoQuery " . $BusinessName);
       $db = new DynamoDB();
       $businessId = $db->getBusinessIdFromName($BusinessName);
       $this->logger->trace("QueryProcessor::processVideoQuery " . $businessId);
       $db->getVideo($businessId,0);
   }
 }

}
?>
