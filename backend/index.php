<?php
    //this file containes handler functions for each routes
    require "handlers.php";
    $handler=new handler();

    //acceptable routes in our system
    $routes=array("index"=>"/","signup"=>"/createUser","login"=>"/createSession","logout"=>"/destroySession");
 
    //extracting the url from the request
    $url = $_SERVER["REQUEST_URI"];
    //extracting the request method.
    $method = $_SERVER["REQUEST_METHOD"];

    //based on the url it's specific handler will be called. It the url asked is not present in our routes exceptionalPath handler will be called.
    if($url==$routes["signup"]){
        $handler->createUser($method);
    }
    else if($url==$routes["index"]){
        $handler->indexRoute();
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