<?php
require_once('./includes/api.lib.controller.php');
$requestObj = new ReqController();

if( ! $requestObj->isAuthorized() ) {
  ReqController::respond( 400 );
  exit;
}

switch( $requestObj->getApiVersion() ) {
 case 'v1':
    switch( $requestObj->getHttpMethod() ){
        case 'post':
             switch( $requestObj->getApiMethod() ) {
                  case 'survey':
                      if( !$requestObj->validQueryParameters() ) {
                          ReqController::respond( 400 );
                          break;
                       }
                       if( !$requestObj->updateDB() ) {
                          ReqController::respond( 400 );
                          break;
                       }
                       ReqController::respond( 200 );
                       break;
                  case 'video':
                       if( !$requestObj->validVideo() ) {
                          ReqController::respond( 400 );
                          break;
                       }
                       if( !$requestObj->updateS3() ) {
                          ReqController::respond( 400 );
                          break;
                       }
                       ReqController::respond( 200 );
                       break;
                  default:
                         ReqController::respond( 400 );
                          break;
             }
             break;

        case 'get':
             switch( $requestObj->getApiMethod() ) {
                 case 'survey':
                      if( !$requestObj->getSurvey() ) {
                          ReqController::respond( 400 );
                      }
                      else {
                          ReqController::respond( 200 );
                      }
                      break;
                 case 'video':
                      if( !$requestObj->getVideo() ) {
                          ReqController::respond( 400 );
                      }
                      else {
                          ReqController::respond( 200 );
                      }
                      break;
                 default:
                      ReqController::respond( 400 );
                      break;
             }
	     break; 
        default:
             ReqController::respond( 400 );
             break;
   }
   break;
 default:
   ReqController::respond( 400 );
   break;
}
exit; 
?>
