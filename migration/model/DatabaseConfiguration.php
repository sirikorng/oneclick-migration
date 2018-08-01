<?php
/**
 * @author Sirikorn G
 *
 */

require_once('dupx/class.dupx.log.php');

Class DatabaseConfiguration{
    private $dbcharset, $dbcollate ,$dbcollatefb, $root_path, $dbnbsp;
  
   
    public  function __construct($dbcharset, $dbcollate ,$dbcollatefb, $root_path, $dbnbsp) {
       
        $this->dbcharset = $dbcharset;
        $this->dbcollate = $dbcollate;
        $this->dbcollatefb = $dbcollatefb;
        $this->root_path = $root_path;
        $this->dbnbsp = $dbnbsp;       
        $this->log();
   }
    /**
     * @return mixed
     */
    public function getDbcharset()
    {
        return $this->dbcharset;
    }

    /**
     * @return mixed
     */
    public function getDbcollate()
    {
        return $this->dbcollate;
    }

    /**
     * @return mixed
     */
    public function getDbcollatefb()
    {
        return $this->dbcollatefb;
    }

    /**
     * @return mixed
     */
    public function getRoot_path()
    {
        return $this->root_path;
    }

    /**
     * @return mixed
     */
    public function getDbnbsp()
    {
        return $this->dbnbsp;
    }

    /**
     * @param mixed $dbcharset
     */
    public function setDbcharset($dbcharset)
    {
        $this->dbcharset = $dbcharset;
    }

    /**
     * @param mixed $dbcollate
     */
    public function setDbcollate($dbcollate)
    {
        $this->dbcollate = $dbcollate;
    }

    /**
     * @param mixed $dbcollatefb
     */
    public function setDbcollatefb($dbcollatefb)
    {
        $this->dbcollatefb = $dbcollatefb;
    }

    /**
     * @param mixed $root_path
     */
    public function setRoot_path($root_path)
    {
        $this->root_path = $root_path;
    }

    /**
     * @param mixed $dbnbsp
     */
    public function setDbnbsp($dbnbsp)
    {
        $this->dbnbsp = $dbnbsp;
    }

    public function log(){
        DUPX_Log::info("##### Database Configuration :::::: 
            dbcharset = ". $this->dbcharset.", dbcollate = ".
            $this->dbcollate." , dbcollatefb = ".
            $this->dbcollatefb. " , root_path = ".
            $this->root_path ." , dbnbsp = ".
            $this->dbnbsp ."\n ::::");       
        
    }
   
}