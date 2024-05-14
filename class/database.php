<?php

    class Database {

        const DB_HOST = "3.109.248.123";
        const DB_NAME = "emoney";
        const DB_USER = "emoneywebservice";
        const DB_PASS = "P@ssw0rd!";

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
