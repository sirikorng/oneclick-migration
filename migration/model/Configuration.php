<?php
/**
 * @author Sirikorn G
 *
 */

require_once('dupx/class.dupx.log.php');

Class Configuration{
   private $dbprefix;
   private $blog_name;
   private $url_new;
   private $url_old;
   private $path_new;
   private $path_old;
   private $siteurl;
   private $tables;
   private $fullsearch;
   private $exe_safe_mode;
   
   public  function __construct($dbprefix, $blog_name, $url_new,   $url_old,
    $path_new, $path_old, $siteurl, $tables, $fullsearch, $exe_safe_mode) {
       
        $this->dbprefix = $dbprefix;
        $this->blog_name = $blog_name;
        $this->url_new = $url_new;
        $this->url_old = $url_old;
        $this->path_new = $path_new;
        $this->path_old = $path_old;
        $this->path_new = $path_new;
        $this->path_old = $path_old;
        $this->siteurl = $siteurl;
        $this->tables = $tables;
        $this->fullsearch = $fullsearch;
        $this->exe_safe_mode = $exe_safe_mode;
        $this->log();
   }
/**
     * @return mixed
     */
    public function getDbprefix()
    {
        return $this->dbprefix;
    }

/**
     * @return mixed
     */
    public function getBlog_name()
    {
        return $this->blog_name;
    }

/**
     * @return mixed
     */
    public function getUrl_new()
    {
        return $this->url_new;
    }

/**
     * @return mixed
     */
    public function getUrl_old()
    {
        return $this->url_old;
    }

/**
     * @return mixed
     */
    public function getPath_new()
    {
        return $this->path_new;
    }

/**
     * @return mixed
     */
    public function getPath_old()
    {
        return $this->path_old;
    }

/**
     * @return mixed
     */
    public function getSiteurl()
    {
        return $this->siteurl;
    }

/**
     * @return mixed
     */
    public function getTables()
    {
        return $this->tables;
    }

/**
     * @return mixed
     */
    public function getFullsearch()
    {
        return $this->fullsearch;
    }

/**
     * @return mixed
     */
    public function getExe_safe_mode()
    {
        return $this->exe_safe_mode;
    }

/**
     * @param mixed $dbprefix
     */
    public function setDbprefix($dbprefix)
    {
        $this->dbprefix = $dbprefix;
    }

/**
     * @param mixed $blog_name
     */
    public function setBlog_name($blog_name)
    {
        $this->blog_name = $blog_name;
    }

/**
     * @param mixed $url_new
     */
    public function setUrl_new($url_new)
    {
        $this->url_new = $url_new;
    }

/**
     * @param mixed $url_old
     */
    public function setUrl_old($url_old)
    {
        $this->url_old = $url_old;
    }

/**
     * @param mixed $path_new
     */
    public function setPath_new($path_new)
    {
        $this->path_new = $path_new;
    }

/**
     * @param mixed $path_old
     */
    public function setPath_old($path_old)
    {
        $this->path_old = $path_old;
    }

/**
     * @param mixed $siteurl
     */
    public function setSiteurl($siteurl)
    {
        $this->siteurl = $siteurl;
    }

/**
     * @param mixed $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

/**
     * @param mixed $fullsearch
     */
    public function setFullsearch($fullsearch)
    {
        $this->fullsearch = $fullsearch;
    }

/**
     * @param mixed $exe_safe_mode
     */
    public function setExe_safe_mode($exe_safe_mode)
    {
        $this->exe_safe_mode = $exe_safe_mode;
    }

    public function log(){
        DUPX_Log::info("##### ".get_class($this) ." :::::: ".
            "dbprefix = ".$this->dbprefix.",".
            "blog_name=".$this->blog_name.",".
            "url_new=".$this->url_new.",".
            "url_old=".$this->url_old.",".
            "path_new=".$this->path_new.",".
            "path_old=".$this->path_old.",".
            "siteurl=".$this->siteurl.",".
            "tables=".$this->tables.",".
            "fullsearch=".$this->fullsearch.",".
            "exe_safe_mode=".$this->exe_safe_mode."\n ::::");
            
  
    }
    
}