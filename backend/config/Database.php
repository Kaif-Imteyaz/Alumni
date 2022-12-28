<?php
    class Database{
        private $server="localhost";
        private $username="yusuf";
        private $password = "yusuf";
        function getConnection(){
              $connection = new mysqli($this->server, $this->username, $this->password);
              return $connection;
        }
    }
?>