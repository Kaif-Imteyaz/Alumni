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
                echo $e->getMessage();
            }
        }
    }
?>