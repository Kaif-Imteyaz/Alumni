<?php
    class Database{
        private $server="localhost";
        private $username="root";
        private $password = "";
    private $dbName = "cit";
        public function getConnection(){
            try {
                $connection = new mysqli($this->server, $this->username, $this->password, $this->dbName);
                return $connection;
            }
            catch(Exception $e){
                 http_response_code(500);
                echo $e->getMessage();
                exit;
            }
        }
    }
?>