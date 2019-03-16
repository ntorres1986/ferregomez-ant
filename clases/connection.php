<?php
  class connection
  { 
        private $host = 'localhost';
        private $dbname = 'ferreg_ferregomez';
        private $username = 'root';
        private $password ='nTorres.12';   
        public $con = '';  

        function __construct()
        { 
            $this->connect();   
        } 
        function connect()
        { 
            try
            {                
                $this->con = new PDO("mysql:host=$this->host;dbname=$this->dbname",$this->username, $this->password);
                $this->con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION); 
            }
            catch(PDOException $e)
            { 
                echo 'We\'re sorry but there was an error while trying to connect to the database';
                file_put_contents('connection.errors.txt', date("Y-m-d H:i:s") ." ". $e->getMessage().PHP_EOL,FILE_APPEND); 
            }
        }  
        function query( $query )
        { 
            $data = $this->con->prepare( $query );
            $data->execute(); 

            return $data;
            
        } 
        function getLastId()
        {
           return $this->con->lastInsertId();
        }
        function beginTransaction()
        {
            $this->con->beginTransaction();
        }
        function commit()
        {
            $this->con->commit();
        }
        function rollBack()
        {
            $this->con->rollBack();
        }  
    }
?>