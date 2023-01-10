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
                $this->storeValue($data);
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

    function createSession(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            try{
                $data = json_decode(file_get_contents("php://input"));
                $id = gettype($data->id)=='string' && strlen(trim($data->id))>0 ?trim($data->id):false;
                $email = gettype($data->email)=='string' && strlen(trim($data->email))>0 ?trim($data->email):false;
                $password = gettype($data->password)=='string' && strlen(trim($data->password))>0 ?trim($data->password):false;

                if($id && $email && $password){
                    
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
    
    public function storeValue($data){
        //db is an object of class Database. Database class has a getConnection function which returns connection object.
        try {
            $this->connection = $this->db->getConnection();
            $sql = "insert into " . $data->type . " (name,email,id,password,currentyear,yearofjoining) values (?,?,?,?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->bind_param("ssssis", $data->name, $data->email, $data->id, $data->password, $data->currentYear, $data->yoj);
            $result = $statement->execute();
            $statement->close();
            http_response_code(200);
            echo "Data successfully saved";
        } catch (Exception $e) {
            http_response_code(500);
            echo $e->getMessage();
        }
        finally{
            $this->connection->close();
        }
    }
}
?>