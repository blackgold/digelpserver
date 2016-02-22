<?php
 class VideoQueryJsonResponseBuilder 
 {
   public $jsonResponse = array("vreviews" => array());
   function __construct( $iterator ) 
   {
       foreach ($iterator as $item) {
           $jsonRow = array (
               "Timestamp"  =>  $item['Timestamp']['N'],
               "URL"    =>  $item['Url']['S']
           );
           array_push($this->jsonResponse["reviews"], $jsonRow);
       }
   }
 }
?>
