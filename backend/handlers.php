<?php
require('./config/Database.php');

//Access Modifies in PHP
//public (default)
//priavte 
class handler{
    //this handler will be called for when user hit the /createUser url i.e, when signup button will be clicked.
    private $db;
    function __construct(){
        $db = new Database();
    }
    function createUser(string $method){
        //this function will only be called when the method is post. else 403 response code will be sent to the user.

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //extracting data from the req body. 
            $jsonData = file_get_contents("php://input");
            //converting the json string into php variables.
            $data = json_decode($jsonData);

            //calling validateParameters function for validating each parameter.
            $this->validateParameters($data->type,$data->name, $data->id, $data->email, $data->yoj, $data->password);

            //creating connection with the database.
            $connection = $this->db->getConnection();


        }
        else{
            http_response_code(403);
        }
    }
    
    function indexRoute(){
        echo "this is the home page";
    }
    function exceptionalPath(){
        die("invalid url");
    }
    private function validateParameters($type,$name,$id,$email,$yoj,$password){
        //validating $type [type of account]
        //acceptable type value -> alumini, faculty, student
        $type = gettype($type) == 'string' && array_search($type, ['alumini', 'faculty', 'student']) ? $type : false;
        if(!$type){
            echo "please specify the type of account";
            exit;
        }
        //name should be of string and should not be blank and white space
        $name = gettype($name) == 'string' && strlen(trim($name)) > 0 ? trim($name) : false;
        if(!$name){
            echo "please specify your name";
            exit;
        }
        //id should be string 
        if(gettype($id)!='string'){
            echo "id should be valid string";
            exit;
        }
        if(strlen(trim($id))!=9){
                echo "invalid id";
                exit;
        }
        $id = strlen(trim($id));
    }
}
?>