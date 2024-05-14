<?php

class Mutasi{
    const API_URL = "https://mutasibank.co.id/api/v1";
    const API_KEY = "RkM0S2ROOUFmM1N1TEw3MU5TT1lvazRoYjVHajVrNGQzMFpCWmZFTHFsTm1tRENRVUM3aUFaTkZRSzZ360c340069ba63";
    private $connection = null;

    public function __construct($conn){
        $this->connection = $conn;
    }

    public function GetAccounts(){
        $header = [
            "Authorization: " . self::API_KEY,
        ];

        return self::http_get(self::API_URL . "/accounts", $header);
    }

    public function GetAccount($accountId){
        $header = [
            "Authorization: " . self::API_KEY,
        ];

        return self::http_get(self::API_URL . "/account/$accountId", $header);
    }

    public static function http_post($url, $param = [], $headers = [])
    {
        $response = array();
        //set POST variables
        $fields_string = http_build_query($param);
        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0");
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //execute post
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        return $result;
    }

    public static function http_get($url, $headers = array())
    {

       
        // is cURL installed yet?
        if (!function_exists('curl_init')) {
            die('Sorry cURL is not installed!');
        }

        // OK cool - then let's create a new cURL resource handle
        $ch = curl_init();

        // Now set some options (most are optional)

        // Set URL to download
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Set a referer
        curl_setopt($ch, CURLOPT_REFERER, $url);

        // User agent
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0");

        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);

        // Download the given URL, and return output
        $output = curl_exec($ch);

        // Close the cURL resource, and free system resources
        curl_close($ch);

        return $output;

    }

    public function AddMutasi($systemdate, $description, $type, $amount, $balance, $accountId, $accountNo, $bankCode, $date, $id){
        try{
            $query = "INSERT INTO tbl_mutasi (d_transactiondate, v_description, v_type, n_amount, n_balance, n_accountid, v_accountno, v_bankcode, d_date, v_mutasiid) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(1, $systemdate, PDO::PARAM_STR);
            $stmt->bindValue(2, $description, PDO::PARAM_STR);
            $stmt->bindValue(3, $type, PDO::PARAM_STR);
            $stmt->bindValue(4, $amount, PDO::PARAM_STR);
            $stmt->bindValue(5, $balance, PDO::PARAM_STR);
            $stmt->bindValue(6, $accountId, PDO::PARAM_STR);
            $stmt->bindValue(7, $accountNo, PDO::PARAM_STR);
            $stmt->bindValue(8, $bankCode, PDO::PARAM_STR);
            $stmt->bindValue(9, $date, PDO::PARAM_STR);
            $stmt->bindValue(10, $id, PDO::PARAM_STR);
            $stmt->execute();
            $stmt = null;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function GetMutasi($bankCode, $accountNo, $isMatched = false){
        try{
            $query = "SELECT n_id, d_transactiondate, v_description, v_type, n_amount, n_balance, n_accountid, v_accountno, v_bankcode, v_mutasiid 
            FROM tbl_mutasi WHERE 1=1 ";
            if($bankCode != ''){
                $query .= " AND v_bankcode = '$bankCode' ";
            }
            if($accountNo != ''){
                $query .= " AND v_accountno = '$accountNo' ";
            }
            if(!$isMatched){
                $query .= " AND n_futuretrxid IS NULL ";
            }
            $stmt = $this->connection->prepare($query);
            $stmt->execute();

            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res;

            $stmt = null;
        }catch(Exception $e){
            throw $e;
        }
    }
}

?>