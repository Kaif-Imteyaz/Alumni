<?php
    class Database{
        private $server="localhost";
        private $username="yusuf";
        private $password = "yusuf";
        function getConnection(){
              $connection = new mysqli($this->server, $this->username, $this->password);
              if($connection->error){
                    echo $connection->connect_error;
                    exit;
              }
              return $connection;
        }
    }
?>