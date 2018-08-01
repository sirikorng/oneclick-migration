<?php
/**
 * @author Sirikorn G
 *
 */

require_once('dupx/class.dupx.log.php');

Class WordpressUserData{
   private $wp_user;
   private $wp_pass;
  
   
   public  function __construct($wp_user, $wp_pass) {
       
        $this->wp_user = $wp_user;
        $this->wp_pass = $wp_pass;      
        $this->log();
   }
    public function log(){
        DUPX_Log::info("##### ".get_class($this) ." :::::: ".
            " wp_user = ".$this->wp_user.
            ", wp_pass = ".$this->wp_pass.          
            "\n ::::");       
        
    }
   }