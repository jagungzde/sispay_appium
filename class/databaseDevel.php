<?php

    class DatabaseDevel {

        const DB_HOST = "3.109.24.87";
        const DB_NAME = "smsreader";
        const DB_USER = "emoney";
        const DB_PASS = "Em0ney";

        private $conn = null;

        public function GetConnection(){
            try{
                $this->conn = new PDO("mysql:host=".self::DB_HOST.";dbname=".self::DB_NAME.";charset=utf8", self::DB_USER, self::DB_PASS);
		        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $this->conn;
            }catch(Exception $e){
                throw $e;
            }
        }

    }

?>