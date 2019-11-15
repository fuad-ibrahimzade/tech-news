<?php

namespace App\Models;

use Carbon\Carbon;
use Core\Model;
use PDO;

class Comments extends Model
{
    public static function createDefault(Type $var = null)
    {
        $db = static::getDB();

        $stmt = $db->prepare("CREATE TABLE IF NOT EXISTS comments
            (
                id SERIAL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                message VARCHAR(255) NOT NULL,
                post_id INT,
                parent_comment_id VARCHAR(255),
                child_comment_ids VARCHAR(255),
                date_upd TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_add DATE NOT NULL,
                PRIMARY KEY (id)
            );
            ");
        $stmt->execute();


        $comment_message='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
        $posts=Posts::getAll();
        for ($i=0; $i < count($posts); $i++) {
            
            $stmt = $db->prepare("INSERT INTO comments (name,email,message,post_id,parent_comment_id,
            child_comment_ids,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? , ? );");
            $stmt->execute(array(
                'John Doe',
                'some_email@email.com',
                $comment_message,
                $posts[$i]->id,
                null,
                json_encode([]),
                Carbon::now(),
                Carbon::now()
            )); 
            // $last_inserted_comment=Comments::getById($db->lastInsertId());
            $last_inserted_comment=Comments::getById($db->lastInsertId('comments_id_seq'));

            $stmt = $db->prepare("INSERT INTO comments (name,email,message,post_id,parent_comment_id,
            child_comment_ids,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? , ? );");
            $stmt->execute(array(
                'John Doe',
                'some_email@email.com',
                $comment_message,
                $posts[$i]->id,
                $last_inserted_comment->id,
                json_encode([]),
                Carbon::now(),
                Carbon::now()
            )); 
            // $last_inserted_child_comment=Comments::getById($db->lastInsertId());
            $last_inserted_child_comment=Comments::getById($db->lastInsertId('comments_id_seq'));
            $child_comment_ids=(array)json_decode($last_inserted_comment->child_comment_ids,true);
            $child_comment_ids[]=$last_inserted_child_comment->id;
            $last_inserted_comment->child_comment_ids=json_encode($child_comment_ids);
            Comments::update($last_inserted_comment);

            $stmt = $db->prepare("INSERT INTO comments (name,email,message,post_id,parent_comment_id,
            child_comment_ids,date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? , ? );");
            $stmt->execute(array(
                'John Doe',
                'some_email@email.com',
                $comment_message,
                $posts[$i]->id,
                null,
                json_encode([]),
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
            $stmt = $db->query('SELECT * FROM comments');
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
            // $stmt = $db->query('SELECT * FROM comments ORDER BY rand() LIMIT '.$limit);
            $stmt = $db->query('SELECT * FROM comments ORDER BY random() LIMIT '.$limit);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        catch (\Exception $e){
        //    echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
        return false;
    }

    public static function getById($id) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM comments WHERE id = ?');
        $stmt->execute(array($id));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ)[0];
        return $allIds;
    }

    public static function getByWhere($ckey,$cvalue) {
        $db = static::getDB();
        $stmt = $db->prepare('SELECT * FROM comments WHERE '.$ckey.' = ?');
        $stmt->execute(array($cvalue));
        $allIds = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $allIds;
    }

    public static function update($fields){
        $db = static::getDB();
        foreach ($fields as $key => $value){
            if(!Comments::column_exists($key))continue;
            if($key=='id')continue;
            $sql = "UPDATE comments SET ".$key."=? WHERE id=?";
            $stmt= $db->prepare($sql);
            $stmt->execute([
                $value,
                $fields->id
            ]);
        }
    }

    public static function create($fields){
        
        $db = static::getDB();
        $stmt = $db->prepare("INSERT INTO comments (name,email,message,post_id,parent_comment_id,child_comment_ids,
                date_upd,date_add) 
            VALUES ( ? , ? , ? , ? , ? , ? , ? , ? );");
        $stmt->execute(array(
            $fields->name,
            $fields->email,
            $fields->message,
            (isset($fields->post_id)?$fields->post_id:'-1'),
            (isset($fields->parent_comment_id)?$fields->parent_comment_id:'-1'),
            (isset($fields->child_comment_id)?$fields->child_comment_id:json_encode([])),
            Carbon::now(),
            Carbon::now()
        ));
        // $last_inserted_comment=Comments::getById($db->lastInsertId());
        $last_inserted_comment=Comments::getById($db->lastInsertId('comments_id_seq'));
        return $last_inserted_comment;
    }

    public static function delete($id)
    {

        $db = static::getDB();
        $stmt = $db->prepare( "DELETE FROM comments WHERE id = ?" );
        $stmt->execute(array($id));
        if( ! $stmt->rowCount() ) {return "Deletion failed";}
        else { return true; }
    }

    public static function column_exists($column)
    {
        $db = static::getDB();
        // $stmt = $db->prepare( "SHOW COLUMNS FROM comments LIKE ?" );
        $stmt=$db->prepare("
        SELECT column_name 
        FROM information_schema.columns 
        WHERE table_name='comments' and column_name=?;
        ");
        $stmt->execute(array($column));
        // $stmt_value=count($db->query("SHOW COLUMNS FROM comments LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return true; }
    }

    public static function getDistinctColumn($column)
    {
        $db = static::getDB();
        $stmt = $db->prepare( "SELECT DISTINCT ".$column." FROM comments" );
        $stmt->execute();
        $stmt_value=count($db->query("SHOW COLUMNS FROM comments LIKE '".$column."'")->fetchAll());
        if( ! $stmt->rowCount() ) { return false; }
        else { return $stmt->fetchAll(PDO::FETCH_OBJ); }
    }
}
