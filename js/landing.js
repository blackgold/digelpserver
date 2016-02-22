(function(){
 var app = angular.module('landing',[]);
 app.controller('LandingController',function(){
     this.review = reviews;
 });
 var reviews = [
     {BusinessId:102,Timestamp:1412838697,Survey:{Food:1,Ambiance:1,Service:1}},
     {BusinessId:102,Timestamp:1412838698,Survey:{Food:1,Ambiance:0,Service:0}},
     {BusinessId:102,Timestamp:1412838699,Survey:{Food:1,Ambiance:1,Service:0}},
 ];

})();
