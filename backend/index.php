<?php
    //this file containes handler functions for each routes
    require "handlers.php";

    //handling CORS[cross origin request sharing]
    //browser before sending the actual request send a preflight request with origin request header

    if( $_SERVER['REQUEST_METHOD']=='OPTIONS'){
        header('Access-Control-Allow-Origin:http://127.0.0.1:5500');
        header('Access-Control-Allow-Headers:content-type');
        header('Access-Control-Allow-Method:POST,GET,OPTIONS,DELETE');
        exit;
    }   
    
    if(isset($_SERVER['HTTP_ORIGIN'])){
        header('Access-Control-Allow-Origin:http://127.0.0.1:5500');
    }


    $handler=new handler();

    //acceptable routes in our system
    $routes=array("signup"=>"/createAccount","login"=>"/createSession","logout"=>"/destroySession");
 
    //extracting the url from the request
    $url = $_SERVER["REQUEST_URI"];

    $url = parse_url($url)["path"];

    //extracting the request method.
    $method = $_SERVER["REQUEST_METHOD"];

    //based on the url it's specific handler will be called. It the url asked is not present in our routes exceptionalPath handler will be called.
    if($url==$routes["signup"]){
        $handler->createUser();
    }
    
    else if($url==$routes["login"]){
        $handler->createSession();
    }
    else if($url==$routes["logout"]){
         $handler->destroySession();
    }
    else{
        $handler->exceptionalPath();
    }
?>