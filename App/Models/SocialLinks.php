<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class SocialLinks extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS social_links
            (
                id SERIAL,
                link_type VARCHAR(255) NOT NULL UNIQUE,
                content VARCHAR(255) NOT NULL,
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();

        $social_links=['facebook','twitter','telegram'];
        for ($i=0; $i <count($social_links) ; $i++) {
            $stmt = $db->prepare("INSERT INTO social_links (link_type,content,date_upd,date_add) VALUES ( ? , ? , ? , ? );");
            $stmt->execute(array(
                $social_links[$i],
                'https://default-profile',
                Carbon::now(),
                Carbon::now()
            )); 
        }
    }
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM social_links');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }
    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM social_links WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM social_links WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if($key=='id')continue;
            $sql = "UPDATE social_links SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields['id']
            ]);
        }
    }
}
