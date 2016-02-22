<?php
 require_once('./includes/vendor/apache/log4php/src/main/php/Logger.php');
 class SurveyPostJsonMessage
 {
   public $businessId;
   public $timestamp;
   public $survey;
   public $isOk;
  
   function __construct( $postData ) 
   {
      Logger::configure('./config/config.xml');
      $logger = Logger::getLogger('myLogger');
      $logger->trace("SurveyPostJsonMessage::_construct" . $postData);
      $val = json_decode($postData,true);
      $logger->trace("SurveyPostJsonMessage::_construct " . $val["BusinessId"] . " " . $val["Timestamp"] . " " . json_encode($val["Survey"]) );
      if(!empty($val))
      {
         if(!empty($val["BusinessId"]) && !empty($val["Timestamp"]) && !empty($val["Survey"])) 
         {
           $this->businessId = $val["BusinessId"];
           $this->timestamp=$val["Timestamp"];
           $this->survey=json_encode($val["Survey"]);
           $this->isOk=1;
           $logger->trace("SurveyPostJsonMessage::_construct " . $this->survey  . " " . $this->businessId . " " . $this->timestamp);
         }
      }
   }
 }
?>
