<?php

class Common
{

    private function GetAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function GetBearerToken()
    {
        $headers = $this->GetAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
            if (preg_match('/bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * @param int $length 
     * @return string
     */
    public function GetRandomString($length)
    {
        $ValidChar = "ABCDEFGHJKLMNPRTUVWXYZ2346789";
        $len = strlen($ValidChar);
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            $result .= substr($ValidChar, rand() % $len, 1);
        }
        return $result;
    }

    public function CreateToken($username)
    {
        return sha1($username . date('YmdHis'));
    }

    public function ValidateNumeric($number)
    {
        $arr = explode(".", $number);
        $gotDecimal = count($arr) > 1;

        $balanceWithoutDecimal = $arr[0];

        // $decimal = $arr[1];

        $arr2 = explode(",", $balanceWithoutDecimal);

        $suspectedBalance = false;

        // if($gotDecimal){

        // }

        if (count($arr2) > 1) {
            for ($i = 0; $i < count($arr2); $i++) {
                if ($i == 0) {
                    if (strlen($arr2[$i] . '') > 3) $suspectedBalance = true;
                } else {
                    if (strlen($arr2[$i] . '') != 3) $suspectedBalance = true;
                }
            }
        }

        return $suspectedBalance;
    }

    public function SendCallback($apiUrl, $params, $asJson = true)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($asJson) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);

            $response = "";
            if (!$result) {
                $response = '{"status":"error", "code":' . curl_errno($ch) . ', "messages":' . json_encode(curl_error($ch)) . '}';
            } else {
                $response = '{"status":"success"}';
            }

            curl_close($ch);
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function WriteLog($path, $content, $withDate = true)
    {
        $fp = fopen($path, 'a');
        if (isset($fp)) {
            if ($withDate)
                fwrite($fp, '[' . date("Y-m-d H:i:s") . '] ' . $content . PHP_EOL);
            else
                fwrite($fp, '' . $content . PHP_EOL);
            fclose($fp);
        }
    }

    public function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function ParseDate($date)
    {
        $arr = explode(" ", $date);
        $arr1 = explode("-", $arr[0]);

        return array("year" => intval($arr1[0]), "month" => intval($arr1[1]), "date" => intval($arr1[2]));
    }

    public function ConvertAppiumDateIntoDatetime($appiumDate)
    {
        $tmpDate = $appiumDate;

        $year = date('Y');

        $arr = explode(", ", $tmpDate);

        $day = date('d');
        $month = date('m');

        if ($arr[0] == "Today") {
            $currentTimestamp = time();
            $year = date('Y', strtotime('-2 hours', $currentTimestamp));
            $month = date('m', strtotime('-2 hours', $currentTimestamp));
            $day = date('d', strtotime('-2 hours', $currentTimestamp));
        } else if ($arr[0] == "Yesterday") {
            $currentTimestamp = time();
            $year = date('Y', strtotime('-26 hours', $currentTimestamp));
            $month = date('m', strtotime('-26 hours', $currentTimestamp));
            $day = date('d', strtotime('-26 hours', $currentTimestamp));
        } else {

            $year = date('Y');
            $arr1 = explode(" ", $arr[0]);

            $day = intval($arr1[1]) < 10 ? '0' . $arr1[1] : $arr1[1];
            $month = "01";

            switch ($arr1[0]) {
                case "Jan":
                    $month = "01";
                    break;
                case "Feb":
                    $month = "02";
                    break;
                case "Mar":
                    $month = "03";
                    break;
                case "Apr":
                    $month = "04";
                    break;
                case "May":
                    $month = "05";
                    break;
                case "Jun":
                    $month = "06";
                    break;
                case "Jul":
                    $month = "07";
                    break;
                case "Aug":
                    $month = "08";
                    break;
                case "Sep":
                    $month = "09";
                    break;
                case "Oct":
                    $month = "10";
                    break;
                case "Nov":
                    $month = "11";
                    break;
                case "Dec":
                    $month = "12";
                    break;
            }
        }


        $tmp2 = explode(" ", $arr[1]);
        $jamminute = $tmp2[0];
        $ampm = $tmp2[1];

        $arr2 = explode(":", $jamminute);

        $jam = $arr2[0];
        $minute = $arr2[1];

        if ($ampm == "AM" && $jam == "12") $jam = "00";
        else if ($ampm == "PM") {
            if (intval($jam) != 12) {
                $jam = (intval($jam) + 12) . "";
            }
        }

        $jam = intval($jam);
        $jam = $jam < 10 ? '0' . $jam : $jam;


        $date = $year . "-" . $month . "-" . $day . " " . $jam . ":" . $minute . ":00";

        return $date;
    }

    public function ConvertAppiumDateIntoDatetimeBkash($appiumDate)
    {
        //02:05PM 06/05/24
        $tmpDate = $appiumDate;

        $arr = explode(" ", $tmpDate);

        $arrDate = explode("/", $arr[1]);

        $year = "20" . $arrDate[2];
        $month = $arrDate[1];
        $day = $arrDate[0];

        $ampm = substr($arr[0], -2);
        $time = substr($arr[0], 0, 5);

        $arrTime = explode(":", $time);

        $hour = $ampm == "AM" ? $arrTime[0] : (intval($arrTime[0]) + 12) . "";
        $minutes = $arrTime[1];

        $date = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minutes . ":00";

        return $date;
    }
}
