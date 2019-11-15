<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class Advertisements extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS advertisements
            (
                id SERIAL,
                ads_name_1 VARCHAR(255),
                ads_name_2 VARCHAR(255),
                ads_picture_1 VARCHAR(255),
                ads_picture_2 VARCHAR(255),
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();


        $baseUrl='https://'.$_SERVER['SERVER_NAME'].'/';
        $adds=[];
        for ($i=0; $i < 2 ; $i++) {
            $adds[]=['name'=>'ad'.($i+1),'picture_url'=>$baseUrl.'assets/img/ad-'.($i+1).'.jpg'];
            // evvel buda elave prefix idi: frontend/dist/frontend/
        }
        // for ($i=0; $i < count($adds); $i++) {
            
            $stmt = $db->prepare("INSERT INTO advertisements (ads_name_1,ads_name_2,ads_picture_1,ads_picture_2,
            date_upd,date_add) VALUES ( ? , ? , ? , ? , ? , ? );");
            $stmt->execute(array(
                $adds[0]['name'],
                $adds[1]['name'],
                $adds[0]['picture_url'],
                $adds[1]['picture_url'],
                Carbon::now(),
                Carbon::now()
            )); 
        // }

        
    }
    
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM advertisements');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getRandomLimit($limit)
    {
        $db = static::getDB();
        $stmt=null;
        try{
            // $stmt = $db->query('SELECT * FROM advertisements ORDER BY rand() LIMIT '.$limit);
            $stmt = $db->query('SELECT * FROM advertisements ORDER BY random() LIMIT '.$limit);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM advertisements WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM advertisements WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if(!Advertisements::column_exists($key))continue;
            if($key=='id')continue;
            $sql = "UPDATE advertisements SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields->id
            ]);
        }
    }

    public static function create($fields){
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO advertisements (ads_name_1,ads_name_2,ads_picture_1,ads_picture_2,
        date_upd,date_add) VALUES ( ? , ? , ? , ? , ? , ? );");
        $stmt->execute(array(
            (isset($fields->ads_name_1)?$fields->ads_name_1:''),
            (isset($fields->ads_name_2)?$fields->ads_name_2:''),
            (isset($fields->ads_picture_1)?$fields->ads_picture_1:''),
            (isset($fields->ads_picture_2)?$fields->ads_picture_2:''),
            Carbon::now(),
            Carbon::now()
        ));
    }

    public static function delete($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "DELETE FROM advertisements WHERE id = ?" );
        $stmt->execute(array($id));
        if( ! $stmt->rowCount() ) {return "Deletion failed";}
        else { return true; }
    }

    public static function column_exists($column)
    {
        $db = static::getDB();
        // $stmt = $db->prepare( "SHOW COLUMNS FROM advertisements LIKE ?" );
        $stmt=$db->prepare("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name='advertisements' and column_name=?;
        ");
        $stmt->execute(array($column));
        // $stmt_value=count($db->query("SHOW COLUMNS FROM advertisements LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return true; }
    }
    
    public static function getDistinctColumn($column)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "SELECT DISTINCT ".$column." FROM advertisements" );
        $stmt->execute();
        // $stmt_value=count($db->query("SHOW COLUMNS FROM advertisements LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return $stmt->fetchAll(PDO::FETCH_OBJ); }
    }
}
