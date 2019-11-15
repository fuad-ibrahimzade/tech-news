<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class Pages extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        // posts_count INT(11) NOT NULL,//mysql
        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS pages
            (
                id SERIAL,
                posts_count INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                category_color VARCHAR(255) NOT NULL,
                show_at_home BOOLEAN NOT NULL,
                showing_order INT NOT NULL,
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();

        $pages=['News','Popular','Web Design','JavaScript','Css','Jquery'];
        for ($i=0; $i <count($pages) ; $i++) {
            $stmt = $db->prepare("INSERT INTO pages (name,posts_count,category_color,show_at_home,showing_order,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? );");
            $stmt->execute(array(
                $pages[$i],
                0,
                self::randomColor(),
                1,
                $i+1,
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
            $stmt = $db->query('SELECT * FROM pages');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }
    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM pages WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if($key=='id')continue;
            $sql = "UPDATE pages SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields['id']
            ]);
        }
    }

    public static function create($fields){
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO pages (name,posts_count,category_color,show_at_home,showing_order,date_upd,date_add) 
        VALUES ( ? , ? , ? , ? , ? , ? , ? );");
        $stmt->execute(array(
            $fields->name,
            0,
            self::randomColor(),
            $fields->show_at_home,
            $fields->showing_order,
            Carbon::now(),
            Carbon::now()
        ));
    }

    public static function delete($id)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "DELETE FROM pages WHERE id = ?" );
        $stmt->execute(array($id));
        if( ! $stmt->rowCount() ) {return "Deletion failed";}
        else { return true; }
    }

    private static function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
    
    private static function random_color() {
        return self::random_color_part() . self::random_color_part() . self::random_color_part();
    }

    protected static function randomColor(){
        $result = array('rgb' => [], 'hex' => '');
        foreach(array('r', 'b', 'g') as $col){
            $rand = mt_rand(0, 255);
            $result['rgb'][$col] = $rand;
            $dechex = dechex($rand);
            if(strlen($dechex) < 2){
                $dechex = '0' . $dechex;
            }
            $result['hex'] .= $dechex;
        }
        return $result['hex'];
    }
}
