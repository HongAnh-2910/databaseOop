<?php

    require_once 'Database.php';

 class Connect extends Database
 {
     protected string $host_server ,$db_name , $username , $password;
     protected static  $instance = null;
     private function __construct(array $config = [])
     {
         $this->host_server =  $config['host_server'];
         $this->db_name =  $config['db_name'];
         $this->username =  $config['username'];
         $this->password =  $config['password'];
         $this->connect();
     }

     /**
      * @param array $config
      * @return static
      */
     public static function getInstance(array $config = [])
     {
         if (empty(self::$instance))
         {
             self::$instance = new Connect($config);
         }
         return self::$instance;
     }

     /**
      * @return string
      */
     private function connect():string
     {
         try {
             $this->conn = new PDO("mysql:host=$this->host_server;
                                    dbname=$this->db_name", $this->username, $this->password);
             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
             return "Connected successfully";
         } catch(PDOException $e) {
             return "Connection failed: " . $e->getMessage();
         }
     }
 }

