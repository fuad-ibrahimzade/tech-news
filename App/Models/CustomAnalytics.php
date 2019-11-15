<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class CustomAnalytics extends Model
{
    protected $fillable = [
        'analytics_type', 'analytics_data'
    ];
    protected $casts = [
        'analytics_data' => 'array'
    ];

    public static function get_location($user_ip){
        $apiKey_ipgeolocationio = "fb27303e07bf4eb4ac60e3bbc7c2885a";
        $ip=(string)CustomAnalytics::get_client_ip_env();

        $ip=$user_ip;
        if($ip=='::1')$ip='5.197.246.25';//az kompumun ipsi
        // 5.197.246.10 yenisi

        // $location = CustomAnalytics::get_geolocation_oldApi($apiKey_ipgeolocationio, $ip);
        $location = CustomAnalytics::get_geolocation_newApi($ip);
        if(strpos($location,'cURL Error #:')===false)
        {
            $decodedLocation = json_decode($location);
        }
        else{
            $decodedLocation="error";
        }

        return $decodedLocation;
    }

    protected static function get_geolocation_oldApi($apiKey=null, $ip, $lang = "en", $fields = "*", $excludes = "") {
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=".$apiKey."&ip=".$ip."&lang=".$lang."&fields=".$fields."&excludes=".$excludes;

        $url = 'http://ip-api.com/json/'.$ip;
        $url = 'http://ip-api.com/json/'.$ip.'?fields=8450047';
        $cURL = curl_init();

        curl_setopt($cURL, CURLOPT_URL, $url);
        curl_setopt($cURL, CURLOPT_HTTPGET, true);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        return curl_exec($cURL);
    }

    protected static function get_geolocation_newApi ($ip) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://freegeoip.app/json/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
        return "cURL Error #:" . $err;
        } else {
        return $response;
        }
    }

    protected static function get_client_ip_env() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    protected static function get_client_ip_serv() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS custom_analytics
            (
                id SERIAL,
                analytics_type VARCHAR(255) NOT NULL,
                analytics_data TEXT NOT NULL,
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();

        $http_referer=$_SERVER['HTTP_REFERER'];
        $visited_page_link=0;
        // $user_ip=$request->getClientIp();
        $user_ip=(string)CustomAnalytics::get_client_ip_env();
        $locationInfoJson=CustomAnalytics::get_location($user_ip);
        if(isset($locationInfoJson) && isset($locationInfoJson->country_name)){
            $analytics_data=[
                'visited_page_link' => $_SERVER['REQUEST_URI'],
                'user_ip' => $locationInfoJson->ip,
                'country' => $locationInfoJson->country_name,
                'http_referer' => $_SERVER['HTTP_REFERER'],
                'ip_data' => $locationInfoJson
            ];
        }

        $stmt = $db->prepare("INSERT INTO custom_analytics (analytics_type,analytics_data,date_upd,date_add) 
        VALUES ( ? , ? , ? , ? );");
        $stmt->execute(array(
            'link_click',
            json_encode($analytics_data),
            Carbon::now(),
            Carbon::now()
        ));
        // return CustomAnalytics::getById($db->lastInsertId());
        return CustomAnalytics::getById($db->lastInsertId('custom_analytics_id_seq'));
    }
    
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM custom_analytics');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getById($id) 
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM custom_analytics WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) 
    {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM custom_analytics WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields)
    {
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if(!Posts::column_exists($key))continue;
            if($key=='id')continue;
            $sql = "UPDATE custom_analytics SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields->id
            ]);
        }
    }

    public static function create($fields)
    {
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO custom_analytics (analytics_type,analytics_data,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? );");
        $stmt->execute(array(
            $fields->analytics_type,
            $fields->analytics_data,
            Carbon::now(),
            Carbon::now()
        ));
        // return CustomAnalytics::getById($db->lastInsertId());
        return CustomAnalytics::getById($db->lastInsertId('custom_analytics_id_seq'));
    }

    public static function delete($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "DELETE FROM custom_analytics WHERE id = ?" );
        $stmt->execute(array($id));
        if( ! $stmt->rowCount() ) {return "Deletion failed";}
        else { return true; }
    }

    public static function column_exists($column)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "SHOW COLUMNS FROM custom_analytics LIKE ?" );
        $stmt->execute(array($column));
        $stmt_value=count($db->query("SHOW COLUMNS FROM custom_analytics LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return true; }
    }
    
    public static function getDistinctColumn($column)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "SELECT DISTINCT ".$column." FROM custom_analytics" );
        $stmt->execute(array($column));
        $stmt_value=count($db->query("SHOW COLUMNS FROM custom_analytics LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return $stmt->fetchAll(PDO::FETCH_OBJ); }
    }
}
