<?php
class Database
{
    private static $dbName = null;
    private static $dbHost = null;
    private static $dbUsername = null;
    private static $dbUserPassword = null;
     
    private static $cont  = null;
     
    public function __construct() {
        die('Init function is not allowed');
    }
     
    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {     
        try
        {
          self::$dbName = getenv("MYSQL_DATABASE") ?: 'nodemcu_rfid_iot_projects';
          self::$dbHost = getenv("MYSQL_HOST") ?: 'localhost';
          self::$dbUsername = getenv("MYSQL_USER") ?: 'root';
          self::$dbUserPassword = getenv("MYSQL_PASSWORD") ?: '';

          self::$cont = new PDO(
              "mysql:host=".self::$dbHost.";dbname=".self::$dbName,
              self::$dbUsername,
              self::$dbUserPassword
          ); 
          self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
          die("Database connection failed: " . $e->getMessage()); 
        }
       }
       return self::$cont;
    }
     
    public static function disconnect()
    {
        self::$cont = null;
    }
}
?>
