<?php
    class Database{
        private $server = "localhost";
        private $username = "yusuf";
        private $password = "yusuf";

        public function getConnection(){
            $connection = new mysqli($this->server, $this->username, $this->password);
            return $connection;
        }
    }
?>