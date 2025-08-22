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
        if (null == self::$cont) {     
            try {
                // Load from environment variables (Render -> Environment)
                self::$dbName        = getenv("PGDATABASE") ?: 'nodemcu_rfid_iot_projects';
                self::$dbHost        = getenv("PGHOST") ?: 'localhost';
                self::$dbUsername    = getenv("PGUSER") ?: 'root';
                self::$dbUserPassword = getenv("PGPASSWORD") ?: '';

                $dbPort = getenv("PGPORT") ?: "5432";

                self::$cont = new PDO(
                    "pgsql:host=" . self::$dbHost . ";port=" . $dbPort . ";dbname=" . self::$dbName,
                    self::$dbUsername,
                    self::$dbUserPassword
                );
                self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
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
