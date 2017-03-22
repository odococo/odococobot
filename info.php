<?php
  define('BOT_TOKEN', '262354959:AAGZbji0qOxQV-MwzzRqiWJYdPVzkqrbC4Y');
  define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

  class Database {
    private static $dbName = 'u188732877_loot';
    private static $dbHost = 'mysql.hostinger.it' ;
    private static $dbUsername = 'u188732877_admin';
    private static $dbUserPassword = 'bSi9vOB5UrCH';

    private static $connected  = null;
   // costruttore della classe
    public function __construct() {
      die('Init function is not allowed');
    }
     
    // connesione al database
    public static function connect() {
      // soltanto una connesione è consentita
      if(null == self::$connected) {
        try {
          self::$connected =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);
        } catch(PDOException $e) {
          die($e->getMessage());
        }
      }
      return self::$connected;
    }

    // disconnessione dal database
    public static function disconnect() {
      self::$connected = null;
    }
  }