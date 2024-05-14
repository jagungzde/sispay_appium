<?php

    class DatabaseAppium {

        const DB_HOST = "13.235.89.56";
        const DB_NAME = "emoney_appium";
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