<?php
    class Database{
        private $server="localhost";
        private $username="root";
        private $password = "";
    private $dbName = "cit";
        public function getConnection(){
              $connection = new mysqli($this->server, $this->username, $this->password,$this->dbName);
              if($connection->error){
                    echo $connection->connect_error;
                    exit;
              }
              return $connection;
        }
    }
?>