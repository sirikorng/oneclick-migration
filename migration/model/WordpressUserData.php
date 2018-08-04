<?php
/**
 * @author Sirikorn G
 *
 */

require_once('dupx/class.dupx.log.php');

Class WordpressUserData{
    private $wpuser;
    private $wppass;
    
    
    public  function __construct($wpuser, $wppass) {
        
        $this->wpuser = $wpuser;
        $this->wppass = $wppass;
        $this->log();
    }
    
    
    /**
     * @return mixed
     */
    public function getWpuser()
    {
        return $this->wpuser;
    }

    /**
     * @return mixed
     */
    public function getWppass()
    {
        return $this->wppass;
    }

    /**
     * @param mixed $wpuser
     */
    public function setWpuser($wpuser)
    {
        $this->wpuser = $wpuser;
    }

    /**
     * @param mixed $wppass
     */
    public function setWppass($wppass)
    {
        $this->wppass = $wppass;
    }

    public function log(){
        DUPX_Log::info("##### ".get_class($this) ." :::::: ".
            " wpuser = ".$this->wpuser.
            ", wppass = ".$this->wppass.
            "\n ::::");
        
    }
}