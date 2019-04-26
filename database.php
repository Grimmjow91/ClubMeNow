<?php
class Database 
{
	// these are not the real passwords
	// for real passwords, see file in ../database subdirectory
	
        //000webhost.com
     /*  private static $dbName = 'id8586639_crud_tutorial' ; 
	private static $dbHost = 'localhost' ;
	private static $dbUsername = 'id8586639_grimm';
	private static $dbUserPassword = 'DankMemesTaco';     
      */
    
        
        //local
      private static $dbName = 'crud_tutorial' ; 
	private static $dbHost = 'localhost' ;
	private static $dbUsername = 'root';
	private static $dbUserPassword = '';
       
       
       
	
        //csis server
  /*  private static $dbName = 'nmpremo1355wi19' ; 
	private static $dbHost = '10.8.30.49' ;
	private static $dbUsername = 'nmpremo1355wi19';
	private static $dbUserPassword = 'grimmjow91';
    */
	private static $cont  = null;
	
	public function __construct() {
		exit('Init function is not allowed');
	}
	
	public static function connect()
	{
	   // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);  
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
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
