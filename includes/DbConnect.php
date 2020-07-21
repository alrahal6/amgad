<?php 
class DbConnect
{ 
    private $con;
    function __construct() { }
    function connect()
    {
        include_once dirname(__FILE__) . '/Info.php';
        $this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        //set_charset($this->con,"UTF8");
        if (mysqli_connect_errno()) {
            echo "Failed to connect with database" . mysqli_connect_err();
        } 
        return $this->con;
    }
}
/*class DbConnect
{
    private $con;
    function __construct() { }
    function connect()
    {
        include_once dirname(__FILE__) . '/Info.php';
        //$this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->con = new mysqli(null,
            'root', // username
            'Prem@2512',     // password
            'Amgad',
            null,
            '/cloudsql/al-rahal6:us-central1:root'
            );
        //set_charset($this->con,"UTF8");
        if (mysqli_connect_errno()) {
            echo "Failed to connect with database" . mysqli_connect_error();
        }
        //var_dump($this->con);
        //echo "connected Successfully";
        return $this->con;
    }
}*/
