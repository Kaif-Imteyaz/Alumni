<?php
    //this file containes handler functions for each routes
    require "handlers.php";

    //handling CORS[cross origin request sharing]
    //browser before sending the actual request send a preflight request with origin request header and request method=OPTIONS

    if( $_SERVER['REQUEST_METHOD']=='OPTIONS'){
        header('Access-Control-Allow-Origin:http://127.0.0.1:5500');
        header('Access-Control-Allow-Headers:content-type');
        header('Access-Control-Allow-Methods:DELETE,POST,GET,OPTIONS');
        exit;
    }   
    
    if(isset($_SERVER['HTTP_ORIGIN'])){
        header('Access-Control-Allow-Origin:http://127.0.0.1:5500');
        header('Access-Control-Allow-Headers:content-type');
        header('Access-Control-Allow-Methods:DELETE,POST,GET,OPTIONS');
    }


    $handler=new handler();

    //acceptable routes in our system
    $routes=array("signup"=>"/createAccount","login"=>"/createSession","logout"=>"/destroySession","fileUpload"=>"/fileUpload","getFile"=>"/getFile","getAllFiles"=>"/getAllFiles");
 
    //extracting the url from the request
    $url = $_SERVER["REQUEST_URI"];

    $url = parse_url($url)["path"];

    //extracting the request method.
    $method = $_SERVER["REQUEST_METHOD"];

    //based on the url it's specific handler will be called. It the url asked is not present in our routes exceptionalPath handler will be called.
    //signup
    if($url==$routes["signup"]){
        $handler->createUser();
    }

    //login
    else if($url==$routes["login"]){
        $handler->createSession();
    }

    //logout
    else if($url==$routes["logout"]){
         $handler->destroySession();
    }

    //fileupload
    else if($url==$routes["fileUpload"]){
        $handler->fileUpload();
    }

    //retreive a specific file
    else if($url==$routes['getFile']){
        $handler->getFile();
    }

    else if($url==$routes['getAllFiles']){
        $handler->getAllFiles();
    }

    else{
        $handler->exceptionalPath();
    }
?>