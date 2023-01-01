<?php
require('./config/Database.php');


class handler{
    //this handler will be called for when user hit the /createUser url i.e, when signup button will be clicked.
    private $db;
    function __construct(){
        $db = new Database();
    }
    function createUser(){
        //this function will only be called when the method is post. else 403 response code will be sent to the user.
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //extracting data from the req body. 
            $jsonData = file_get_contents("php://input");
            //converting the json string into php variables.
            $data = json_decode($jsonData);

            //calling validateParameters function for validating each parameter.
            $this->validateParameters($data);

            echo json_encode($data);
            //storing the value to the 
            // $this->storeValue($data);
        }
        else{
            http_response_code(403);
            echo "Invalid Method";
        }
    }
    
    //this handler will be called whenever user hit url not specified in our system.
    function exceptionalPath(){
        die("invalid url");
    }
    private function validateParameters($data){
        $type = $data->type;
        $name = $data->name;
        $email = $data->email;
        $id = $data->id;
        $yoj = $data->yoj;
        $password = $data->password;
        //validating $type [type of account]
        //acceptable type value -> alumini, faculty, student
        $type = gettype($type) == 'string' && array_search($type, ['alumini', 'faculty', 'student']) ? $type : false;
        if(!$type){
            echo "please specify the type of account";
            exit;
        }
        //name should be of string and should not be blank and white space
        $name = gettype($name) == 'string' && strlen(trim($name)) > 0  ? trim($name) : false;
        if(!$name){
            echo "please specify your name";
            exit;
        }
        //name should not be greater than 15 characters long
        if(strlen($name)>15){
            echo "name should not be more than 15 characters";
            exit;
        }
        //id should be string 
        if(gettype($id)!='string'){
            echo "id should be valid string";
            exit;
        }
        //the length of id is set to be 9. [202003131]--> 9 characters
        if(strlen(trim($id))!=9){
                echo "invalid id";
                exit;
        }
        //trim removes white spaces from the left and right of the string.
        $id = trim($id);

        //email should not be empty string.
        if(strlen(trim($email))<=0){
            echo "please enter your email";
            exit;
        }

        $email=trim($email);
        //checking for the valid format of the email
        if(!preg_match("/[a-zA-z0-9+_.-]+@[a-zA-Z0-9.-]+$/", $email)){
            echo "please enter a valid email [special characters except \"+.-_@\" are not allowed]";
            exit;
        }


        //checking for the emptiness of year of joining input
        $yoj = gettype($yoj) == 'string' && strlen(trim($yoj)) > 0 ? trim($yoj) : false;
        if(!$yoj){
            echo "please specify your year of joining";
            exit;
        }
        //---determining the current year of the student----

        $yoj = date_parse($yoj);
        $currentDate =date_parse(date('d-m-Y'));
        $currentYear = false;

        //checking if the year of joining is less than or equal to the current year.
        if($currentDate['year']>=$yoj['year']){
            if($currentDate['year']==$yoj['year'] && $currentDate['month']<$yoj['month']){
                echo "You are yet to join Polytechnic!";
                exit;
            }
            else{
                $currentYear = $currentDate['year'] - $yoj['year'];
            }
        }
        // if year of joining is greater than the current year the student hasn't join the polytechnic yet.
        else{
            echo "You are yet to join Polytechnic!";
            exit;
        }
        //current year variable stores the current year of the student. This variable needs to be updated each year.
        if(gettype($currentYear)=='boolean' && !$currentYear){
            echo $currentYear;
            exit;
        }

        $password = gettype($password) == 'string' && strlen(trim($password)) > 0 ? trim($password) : false;
        if(!$password){
            echo "please enter your password";
            exit;
        }
      
        if(strlen($password)<10){
            echo "password should be atleast 10 characters long";
            exit;
        }

        //hashing the password
         $password = sha1($password);
        
         //changing property of the data object with their new corresponding value.
         $data->type=$type;
         $data->name=$name;
         $data->email=$email;
         $data->id=$id;
         $data->currentYear=$currentYear;
         $data->password=$password;
    }
    private function storeValue($data){
        //db is an object of class Database. Database class has a getConnection function which returns connection object.
        $connection = $this->db->getConnection();
        $sql = "insert into " . $data->type . " (name,email,id,password,year) values (?,?,?,?)";
        $statement = $connection->prepare($sql);
        $statement->bind_param("ssssi", $data->name, $data->email, $data->id, $data->password, $data->currentYear);

        $result=$statement->execute();
        echo "Data successfully saved";
        var_dump($result);

        $statement->close();
        $connection->close();
    }
}
?>