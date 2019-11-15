<?php

namespace Core;

use PDO;
use App\Config;

abstract class Model
{
    protected static function getDB()
    {
        static $db = null;
        if ($db === null) {
            try{
                // $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';
                // $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
                $dsn = 'pgsql:host=' . Config::DB_HOST . ';port=' . Config::DB_POSTGRESQL_PORT . ';dbname=' . Config::DB_NAME . 
                ';user=' . Config::DB_USER . ';password=' . Config::DB_PASSWORD;
                $db = new PDO($dsn);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOException $e){
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
        return $db;
    }

    public static function getPostgresqlTables($pdo) {
        $stmt = $pdo->query("SELECT table_name 
                                   FROM information_schema.tables 
                                   WHERE table_schema= 'public' 
                                        AND table_type='BASE TABLE'
                                   ORDER BY table_name");
        $tableList = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tableList[] = $row['table_name'];
        }
 
        return $tableList;
    }
}
