<?php

namespace App\Models;

use App\Config;
use Carbon\Carbon;
use PDO;

class User extends \Core\Model
{
    public static function createAdmin(){
        $db = static::getDB();

        // $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS `users`
        //     (
        //         `id` INT(11) NOT NULL AUTO_INCREMENT,
        //         `email` VARCHAR(255) NOT NULL,
        //         `password` VARCHAR(60) NOT NULL,
        //         `api_token` VARCHAR(255) NOT NULL,
        //         `date_upd` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        //         `date_add` DATETIME NOT NULL,
        //         PRIMARY KEY (`id`),
        //         UNIQUE KEY `email` (`email`)
        //     ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT = 65;
        //     ");
        // yuxaridaki `` qushlariynan olan mysql versiyadi
        $stmt = "CREATE TABLE IF NOT EXISTS users
        (
            id SERIAL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(60) NOT NULL,
            api_token VARCHAR(255) NOT NULL,
            date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            date_add DATE NOT NULL,
            PRIMARY KEY (id)
        );
        ";
        // $stmt->execute();
        // yuxaridaki postgresqlcun ishlemir
        $db->exec($stmt);
        // $stmt = $db->prepare("INSERT INTO `users` (`email`, `password`,`api_token`,`date_upd`,`date_add`) VALUES ( ? , ?, ? , ? , ? );");
        $stmt = $db->prepare("INSERT INTO users (email, password,api_token,date_upd,date_add) VALUES ( ? , ?, ? , ? , ? );");
        $stmt->execute(array(
            htmlspecialchars(strip_tags(Config::ADMIN_EMAIL)),
            htmlspecialchars(strip_tags(password_hash(Config::ADMIN_PASSWORD, PASSWORD_DEFAULT))),
            htmlspecialchars(strip_tags(hash("sha256", rand()))),
            htmlspecialchars(strip_tags(Carbon::now())),
            htmlspecialchars(strip_tags(Carbon::now()))
        ));

//        echo password_hash('rasmuslerdorf', PASSWORD_DEFAULT)."\n";
        if(!About::getAll()){
            About::createDefault();
        }
        if(!Contacts::getAll()){
            Contacts::createDefault();
        }
        if(!SocialLinks::getAll()){
            SocialLinks::createDefault();
        }
        if(!Pages::getAll()){
            Pages::createDefault();
        }
        if(!Posts::getAll()){
            Posts::createDefault();
        }
        if(!Comments::getAll()){
            Comments::createDefault();
        }
        if(!CustomAnalytics::getAll()){
            CustomAnalytics::createDefault();
        }
        if(!Advertisements::getAll()){
            Advertisements::createDefault();
        }

    }
    public static function getAll()
    {
        
        $db = static::getDB();
        $stmt=null;

        // $stmt= $db->query("SHOW TABLES LIKE 'users'");
        // $tableExists = $stmt !== false && $stmt->rowCount() > 0;
        // if(!$tableExists){
        //     return false;
        // }
        try{
            // id, email, api_token
            $stmt = $db->query('SELECT * FROM users');
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
//            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }
    public static function getUserByEmail($email) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute(array($email));
        $allEmails = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
//        var_dump($allEmails);
        return $allEmails;
    }
    
    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if($key=='email')continue;
            // $sql = "UPDATE `users` SET `".$key."`=? WHERE `email`=?";
            $sql = "UPDATE users SET ".$key."=? WHERE email=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields['email']
            ]);
        }

    }
}
