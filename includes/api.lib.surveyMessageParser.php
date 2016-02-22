<?php
 class SurveyData 
 {
   public $isFoodGood;
   public $isServiceGood;
   public $isAmbianceGood;
   public $businessId;
   public $timestamp;
   public $isOk;
  
   function __construct( $postData ) 
   {
      #$postData = str_replace(array("\n","\r"),"\\n",$postData);
      #{"BusinessId" : "102", "Timestamp" : "1412838697", "Food" = "1", "Ambiance" : "1", "Service" : "1" }
      $val = json_decode($postData,TRUE);
      if(!empty($val))
      {
         if(!empty($val["BusinessId"]) && !empty($val["Food"]) && !empty($val["Ambiance"]) && !empty($val["Service"])) 
         {
           $this->isFoodGood = $val["Food"];
           $this->isServiceGood = $val["Service"];
           $this->isAmbianceGood = $val["Ambiance"];
           $this->businessId = $val["BusinessId"];
           $this->isOk=1;
           $this->timestamp=$val["Timestamp"];
         }
      }
   }
 }
?>
