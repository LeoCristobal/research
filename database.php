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
                // Render environment variables with fallback to credentials
                self::$dbName         = getenv("PGDATABASE") ?: 'nodemcu_rfid_iot_projects';
                self::$dbHost         = getenv("PGHOST") ?: 'dpg-d2k4jnjuibrs73efg0ag-a.oregon-postgres.render.com';
                self::$dbUsername     = getenv("PGUSER") ?: 'root';
                self::$dbUserPassword = getenv("PGPASSWORD") ?: 'mIz1KjtqN5tibyhVNRj94maun3pxPMFk';
                $dbPort               = getenv("PGPORT") ?: "5432";

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
