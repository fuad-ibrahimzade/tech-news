<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class Contacts extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS contacts
            (
                id SERIAL,
                content TEXT NOT NULL,
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();
        $stmt = $db->prepare("INSERT INTO contacts (content,date_upd,date_add) VALUES ( ? , ? , ? );");
        $stmt->execute(array(
            '
            <h3 _ngcontent-uex-c6="">Contact Information</h3><p _ngcontent-uex-c6="">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p><ul _ngcontent-uex-c6="" class="list-style"><li _ngcontent-uex-c6=""><p _ngcontent-uex-c6=""><strong _ngcontent-uex-c6="">Email:</strong><a _ngcontent-uex-c6="" href="#">Webmag@email.com</a></p></li><li _ngcontent-uex-c6=""><p _ngcontent-uex-c6=""><strong _ngcontent-uex-c6="">Phone:</strong> 213-520-7376</p></li><li _ngcontent-uex-c6=""><p _ngcontent-uex-c6=""><strong _ngcontent-uex-c6="">Address:</strong> 3770 Oliver Street</p></li></ul>
            ',
            Carbon::now(),
            Carbon::now()
        ));
    }
    public static function getAll()
    {
        $db = static::getDB();
        $stmt=null;
        try{
            $stmt = $db->query('SELECT * FROM contacts');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }
    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM contacts WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if($key=='id')continue;
            $sql = "UPDATE contacts SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields['id']
            ]);
        }
    }
}
