<?php
use LDAP\Result;
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
                        //  echo $result->fetch_assoc()['password'];
                        //  exit;

                         if($row=$result->fetch_assoc()){
                             //user exits 
                             //matching the password
                            $original_password = $row['password'];

                             if(sha1($password)==$original_password){
                                 //user entered the correct password
 
                                  //generate user token
                                 //NOTE: the length of token is 10 characters long.
                                 $token=$this->helper->generate_token();
                                 
                                 //store the token to the database
                                 $this->storeToken($result->fetch_assoc()['id'],$type,$token);
 
                                 //After the token has successully been saved into the database.
                                 //create a tokenPayload object [stdClass allows to create object without any class]
                                 $tokenPaylod = new stdClass();
                                 //add properties such as token,id of the user, and type of account of the user.
                                 $tokenPaylod->token = $token;
                                 $tokenPaylod->id = $result->fetch_assoc()['id'];
                                 $tokenPaylod->type = $type;
                                 
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
    
    //this handler will be called whenever user hit url not specified in our system.
    function exceptionalPath(){
        die("invalid url");
    }
    
    public function storeUserData($data){
        //db is an object of class Database. Database class has a getConnection function which returns connection object.
        try {

            $this->connection = $this->db->getConnection();

            $sql = "insert into ".$data->type." (name,email,id,password,currentyear,yearofjoining) values (?,?,?,?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssssis", $data->name, $data->email, $data->id, $data->password, $data->currentYear, $data->yoj);
            $statement->execute();
            $statement->close();
            $this->connection->close();
            http_response_code(200);
            echo "Data successfully saved";
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
      
    }

    function storeToken($id,$type,$token){
        $this->connection = $this->db->getConnection();
        try{
            $statement = $this->connection->prepare("insert into session (type,id,token) values(?,?,?)");
            $statement->bind_param("sss", $type, $id, $token);
            //if the token already exists into the session table it will throw an exception.
            $statement->execute();
            $result = $statement->affected_rows();
            if($result>0){
                echo "token saved successfully";
            }
            $statement->close();
            $this->connection->close();
        }
        catch(Exception $e){
            $statement->close();
            $this->connection->close();
            http_response_code(500);
            echo $e->getMessage();
            exit;
        }
    }
}
?>