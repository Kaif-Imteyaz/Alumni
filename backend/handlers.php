<?php
require('./config/Database.php');
require('./helper.php');

class handler{
    //this handler will be called for when user hit the /createUser url i.e, when signup button will be clicked.
    private $db;
    private $helper;
    private $connection;

    function __construct(){
        $this->db = new Database();
        $this->helper = new Helper();
    }
    function createUser(){
        //this function will only be called when the method is post. else 403 response code will be sent to the user.
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            //extracting data from the req body. 
            try{
                $jsonData = file_get_contents("php://input");
                 //converting the json string into php variables.
                $data = json_decode($jsonData);

                //calling validateParameters function for validating each parameter.
                $this->helper->validateParameters($data);

                //storing the value to the 
                $this->storeUserData($data);

                http_response_code(200);
                echo "user registered successfully";
                exit;
            }
            catch(Exception $e){
                http_response_code(500);
                echo $e->getMessage();
                exit;
            }
        }
        else{
            http_response_code(405);
            echo "Invalid Method";
            exit;
        }
    }

    //this handler is for processing post request made to /createSession made by the client for logging into the system
    function createSession(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            try{
                //can throw exception if the client sends the data in format other than json.
                $data = json_decode(file_get_contents("php://input"));

                $email = gettype($data->email)=='string' && strlen(trim($data->email))>0 ?trim($data->email):false;
                $password = gettype($data->password)=='string' && strlen(trim($data->password))>0 ?trim($data->password):false;
                $type = gettype($data->type) == 'string' && array_search($data->type, ['student', 'faculty', 'alumni']) > -1 ? $data->type : false;

                if($email && $password && $type){
                    //checking if the user with the email exits
                    try{
                        $this->connection = $this->db->getConnection();
                       
                        $sql = "select email,password,id from ".$type." where email=?";
                        $statement = $this->connection->prepare($sql);
                        $statement->bind_param('s',$email);
                        $statement->execute();
                       
                         //$statemetn->get_result() returns a mysqli object.
                         $result = $statement->get_result();
                        
                         $statement->close();
                         $this->connection->close();
                
                         if($row=$result->fetch_assoc()){
                             //user exits 
                             //matching the password
                            $original_password = $row['password'];

                             if(sha1($password)==$original_password){
                                 //user entered the correct password
 
                                  //generate user token
                                 //NOTE: the length of token is 10 characters long.
                                 $token=$this->helper->generate_token();
                                 
                                 $tokenExpiry=time() + 24 * 60 * 60;
                                 //store the token to the database
                                 $this->storeToken($row['id'],$type,$token,$tokenExpiry);
 
                                 //After the token has successully been saved into the database.
                                 //create a tokenPayload object [stdClass allows to create object without any class]
                                 $tokenPaylod = new stdClass();
                                 //add properties such as token,id of the user, and type of account of the user.
                                 $tokenPaylod->token = $token;

                                //  $tokenPaylod->id = $row['id'];
                                //  $tokenPaylod->type = $type;
                                 //the token will expire after one day.
                                 $tokenPaylod->expiry = $tokenExpiry;
                                 
                                 //converting the tokenPaylod object into json using json_encode();
                                 $tokenPaylodString = json_encode($tokenPaylod);
                                 
                                 //send the 200 ok status code with the token data to the user.
                                 http_response_code(200);
                                 echo $tokenPaylodString;
                                 exit;
                             }
                             else{
                                 http_response_code(403);
                                 echo "wrong credentials";
                                 exit;
                             }
                         }
                         else{
                             http_response_code(403);
                             echo "user doesn't exits";
                             exit;
                         }
                    }
                    catch(Exception $e){
                        http_response_code(500);
                        echo $e->getMessage();
                        exit;
                    }
                       
                }
                else{
                    http_response_code(400);
                    echo "Required field missing";
                    exit;
                }
            }
            catch(Exception $e){
                http_response_code(500);
                echo $e->getMessage();
                exit;
            }
        }

        else{
            http_response_code(405);
            echo "invalid method";
            exit;
        }
    }
    

    //handler for logout, it will called when user hit /destroysession url
    function destroySession(){
        if($_SERVER['REQUEST_METHOD']=="DELETE"){
            //get the url;
            $url = $_SERVER['REQUEST_URI'];

            //convert the url into associative array in php
            $parsedurl = parse_url($url);
            $param = array();
            //get the query part of the parsedurl and parse it again to get the associative array with all key-value pairs.
            parse_str($parsedurl['query'], $param);

            if(isset($param['token'])){
                //delete all the token from the database
                try {
                    $this->connection = $this->db->getConnection();
                    $sql = "DELETE from token where token=?";
                    $statement = $this->connection->prepare($sql);
                    $statement->bind_param("s", $param["token"]);

                    $statement->execute();
                    $result=$statement->affected_rows;
                    $statement->close();
                    $this->connection->close();

                    http_response_code(200);
                    echo $result;
        
                    exit;
                }
                catch(Exception $e){
                    http_response_code(500);
                    echo $e->getCode();
                    exit;
                }
            }
            else{
                http_response_code(400);
                echo "cannot log you out";
                exit;
            }
        }
        else{
            http_response_code(405);
            echo "invalid method";
            exit;
        }
    }

    //this handler will be called whenever user hit url not specified in our system.
    function exceptionalPath(){
        http_response_code(404);
        echo "resource not found";
        exit;
    }
    
    //this handler is for verifying the token received from the client by matching it with the database.
    function verifyToken(){
        try{
            $data = json_decode(file_get_contents("php://input"));
            if(!isset($data->token)){
                http_response_code(403);
                echo "you are not authorized";
                exit;
            }

            //check if the token is available on the database.
            $this->connection = $this->db->getConnection();
            $sql = "select * from token where token=?";
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("s", $data->token);
            $statement->execute();
            $result = $statement->get_result();

            $statement->close();
            

            //if the number of rows fetched is 0 than forbid the user
            if($result->num_rows<1){
                http_response_code(403);
                echo "invalid token";
                exit;
            }

            //finding if the user with the userId is available on our database[user to which the token belonged]
            $sql = "select id,name,email,yearofjoining from student where id=?";
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("s", $result->fetch_assoc()['id']);
            $statement->execute();

            $result = $statement->get_result();

            $statement->close();
            $this->connection->close();

            if($result->num_rows<1){
                http_response_code(403);
                echo "invalid token";
                exit;
            }
            http_response_code(200);
            header("content-type:application/json");
            echo json_encode($result->fetch_assoc());
            exit;
        }
        catch(Exception $e){
            http_response_code(500);
            print_r($e);
            exit;
        }
    }

    public function storeUserData($data){
        //db is an object of class Database. Database class has a getConnection function which returns connection object.
        try {
            $this->connection = $this->db->getConnection();

            $sql = "insert into ".$data->type." (name,email,id,password,currentyear,yearofjoining) values (?,?,?,?,?,?)";

            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssssis", $data->name, $data->email, $data->userId, $data->password, $data->currentYear, $data->yoj);
            $statement->execute();
            $statement->close();
            $this->connection->close();
         
        } catch (Exception $e) {
            if($e->getCode()==1062){
                http_response_code(400);
                echo "user already exists";
                exit;
            }
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
      
    }

    function storeToken($id,$type,$token,$expiry){
        try{
            $this->connection = $this->db->getConnection();
            $statement = $this->connection->prepare("insert into token (type,id,token,expiry) values(?,?,?,?)");
            $statement->bind_param("sssi", $type, $id, $token,$expiry);
            //if the token already exists into the session table it will throw an exception.
            $statement->execute();
        
            $statement->close();
            $this->connection->close();
        }
        catch(Exception $e){
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }


    //file upload handler
    function fileUpload(){
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            // print_r($_POST);
            // exit;
            if(!isset($_FILES['file']) && !isset($_POST['branch']) && !isset($_POST['semester']) && !isset($_POST['title']) && !isset($_POST['name']) && !isset($_POST['description'])){
                http_response_code(400);
                echo "Missing required filed";
                exit;
            }
            if($_FILES['file']['type']=='application/pdf'){
                $file = fopen($_FILES['file']['tmp_name'],'r');

                //file
                $blobData = fread($file,  $_FILES['file']['size']);

                //person who uploaded the file
                $name = $_POST['name'];

                //branch for which the file has been uploaded.
                $branch = $_POST['branch'];

                //semester for which the file has been uploaded.
                $semester = (int) $_POST['semester'];

                //title include either question paper or notes
                $title = $_POST['title'];

                //small description related to the file telling which subject and exam it is related to.
                $description=$_POST['description'];

                //storing this data into the database.
                $this->connection = $this->db->getConnection();
                $statement = $this->connection->prepare("insert into files (title,branch,semester,description,name,content) values(?,?,?,?,?,?)");
                $statement->bind_param("ssisss", $title, $branch, $semester, $description, $name, $blobData);
                $statement->execute();
                $result = $statement->affected_rows;
                if($result>0){
                    http_response_code(200);
                    // header("content-type:application/pdf");
                    echo "file saved into the database successfully";
                    exit;
                }
                http_response_code(500);
                echo "file not saved into the database";
                exit;
            }
            else{
                http_response_code(400);
                echo "only pdf files are!:)";
                exit;
            }
        }
        else{
            http_response_code(405);
            echo "invalid method";
            exit;
        }
    }

    //getFile handler
    //user will send the id and only that specific file will be fetched and sent.
    function getFile(){
        if($_SERVER['REQUEST_METHOD']=='GET'){
            $url = $_SERVER['REQUEST_URI'];
            $url = parse_url($url);
            $query = $url['query'];
            parse_str($query, $query);


            $sql = "select content from files where id=(?)";
            $this->connection = $this->db->getConnection();
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("s", $query['id']);
            $statement->execute();
            $result = $statement->get_result();

            $statement->close();
            $this->connection->close();
            if($result->num_rows<1){
                http_response_code(404);
                echo "no file found";
                exit;
            }
            http_response_code(200);
            header("content-type:application/pdf");
            print_r($result->fetch_assoc()['content']);
            exit;
        }
        else{
            http_response_code(405);
            echo "invalid method";
            exit;
        }
    }

    //get all files details except the file content
    function getAllFiles(){
        if($_SERVER['REQUEST_METHOD']=='GET'){
            $url = $_SERVER['REQUEST_URI'];
            $url = parse_url($url);
            $query = $url['query'];
            parse_str($query, $query);
            $branch = $query['branch'];
            $semester = $query['semester'];
            $title = $query['title'];
            $page=1;
            $resultPerPage=10;
            $startingPage=($page-1)*$resultPerPage;

            $sql = "select id,name,branch,semester,title from files where branch=(?) and semester=(?) and title=(?) limit ?,?";
            $this->connection = $this->db->getConnection();
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("sssii",$branch,$semester,$title,$startingPage,$resultPerPage);
            $statement->execute();
            $result = $statement->get_result();

            if($result->num_rows<1){
                http_response_code(404);
                echo "no file found";
                exit;
            }
            
            $arr=array();
            http_response_code(200);
            header("content-type:application/json");

            while($row=$result->fetch_assoc()){
                array_push($arr, $row);
            }
            echo json_encode($arr);
            exit;
        }
        else{
            http_response_code(405);
            echo "invalid method";
            exit;
        }
    }
}
?>