<?php
 class SurveyQueryJsonResponseBuilder 
 {
   public $jsonResponse = array("reviews" => array());
   function __construct( $iterator ) 
   {
       foreach ($iterator as $item) {
           $jsonRow = array (
               "Timestamp"  =>  $item['Timestamp']['N'],
               "Survey"    =>  $item['Survey']['S']
           );
           array_push($this->jsonResponse["reviews"], $jsonRow);
       }
       if (empty($this->jsonResponse["reviews"]))  {
           array_push($this->jsonResponse["reviews"],"No rows");
       }
   }
 }
?>
