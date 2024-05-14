<?php

include_once __DIR__."/../class/common.php";
include_once __DIR__."/../class/database.php";
// include_once __DIR__."/../class/databaseArchive.php";

class BaseCtrl {

    protected $databaseClass = null;
    protected $connection = null;
    protected $databaseArchiveClass = null;
    protected $connectionArchive = null;
    protected $commonClass = null;

    public function __construct($conn = null, $connArchive = null){
        if($conn != null){
            $this->connection = $conn;
        }else{
            $this->databaseClass = new Database();
            $this->connection = $this->databaseClass->getConnection();
        }

        if($connArchive != null){
            $this->connectionArchive = $connArchive;
        }else{
            // $this->databaseArchiveClass = new DatabaseArchive();
            // $this->connectionArchive = $this->databaseArchiveClass->getConnection();
        }

        $this->commonClass = new Common();
    }

    public function __destruct(){
        if($this->connection != null){
            $this->connection = null;
        }
        if($this->connectionArchive != null){
            $this->connectionArchive = null;
        }
        $this->commonClass = null;
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function WriteLog($path, $content, $withDate = true){
        $this->commonClass->WriteLog($path, $content, $withDate);
    }
}
