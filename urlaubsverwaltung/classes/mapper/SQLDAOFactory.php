<?php
namespace classes\mapper;

class SQLDAOFactory{
    private static $instance;

    public static function getInstance(){
        if(!self::$instance){
            try{
                self::$instance  = new \mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
                self::$instance->set_charset("utf8");
            }
            catch(Exception $e){
                echo self::$instance->connect_error;
            }
        }
        return self::$instance;
    }
}
?>