<?php
/**
 * @author Sirikorn G
 *
 */

require_once('dupx/class.dupx.log.php');

Class DatabaseConnectionConfiguration{
    private $dbhost, $dbuser, $dbpass, $dbport, $dbname;
  
   
    public  function __construct($dbhost, $dbuser, $dbpass, $dbport, $dbname) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbport = $dbport;
        $this->dbname = $dbname;
        $this->log();
   }
   
   

    /**
     * @return mixed
     */
    public function getDbhost()
    {
        return $this->dbhost;
    }

    /**
     * @return mixed
     */
    public function getDbuser()
    {
        return $this->dbuser;
    }

    /**
     * @return mixed
     */
    public function getDbpass()
    {
        return $this->dbpass;
    }

    /**
     * @return mixed
     */
    public function getDbport()
    {
        return $this->dbport;
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @param mixed $dbhost
     */
    public function setDbhost($dbhost)
    {
        $this->dbhost = $dbhost;
    }

    /**
     * @param mixed $dbuser
     */
    public function setDbuser($dbuser)
    {
        $this->dbuser = $dbuser;
    }

    /**
     * @param mixed $dbpass
     */
    public function setDbpass($dbpass)
    {
        $this->dbpass = $dbpass;
    }

    /**
     * @param mixed $dbport
     */
    public function setDbport($dbport)
    {
        $this->dbport = $dbport;
    }

    /**
     * @param mixed $dbname
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
    }

    public function log(){
        DUPX_Log::info("##### ".get_class($this) ." :::::: ".
            "  dbhost = ".$this->dbhost.
            ", dbuser = ".$this->dbuser.
            ", dbpass = ".$this->dbpass.
            ", dbport = ".$this->dbport.
            ", dbname =". $this->dbname. 
            "\n ::::");       
        
    }
   
}