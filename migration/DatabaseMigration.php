
<?php

/**
 * Include Classes
 **/

require_once('dupx/class.dupx.u.php');
require_once('dupx/class.dupx.server.php');
require_once('dupx/class.dupx.db.php');
require_once('dupx/class.dupx.log.php');
require_once('dupx/class.dupx.updateengine.php');
require_once('dupx/class.dupx.wpconfig.php');
require_once('dupx/class.dupx.serverconfig.php');

require_once('cpanel/class.cpnl.ctrl.php');

/**
 * 
 *
 * Standard: 
 * @link 
 *
 * @package 
 *
 */
class DatabaseMigration
{

    private $CPNL;

    /*
    */
    function __construct() {
        $this->CPNL  = new DUPX_cPanel_Controller();
    }

	/**
	 * MySQL create DB via Cpanel API
	 *
	 */
	public function connectHost($host, $user, $pass, $dbname)
	{
        
		if (!$host || !$user || !$pass || !$dbname)
		{
            DUPX_Log::error('You must insert the no empty values.\n');
		} else {
            $cpnlToken    = $this->CPNL->create_token($host, $user, $pass);
            $cpnlHost     = $this->CPNL->connect($cpnlToken);
         
            $list_dbs = $this->CPNL->list_dbs($cpnlToken);
            if ($list_dbs['status'] == true) {
                $db_exist = 0;
                foreach ($list_dbs['cpnl_api']->cpanelresult->data as $dbs) {
                    if ($dbs->db == $dbname) {
                        $db_exist = 1;
                        break;
                    }
                }
                if ($db_exist) {
                    DUPX_Log::info("The database('$dbname') already exist.\n");
                } else {                    
                    $result =$this->CPNL->create_db($cpnlToken, $dbname);
                    if ($result['status'] != 1) {
                        DUPX_Log::error("\nCpanel Information has a wrong data(Create DB): '{$result['status']}'");
                    }
                }
            } else {
                DUPX_Log::error("\nCpanel infromation is wrong: {$list_dbs['status']}");
            }
        }
        return $cpnlToken;
    }

    /**
	 * MySQL grant database User
	 *
	 * @return database connection handle
	 */
	public function grantDatabaseUser($token, $dbname, $user)
	{
        $result = $this->CPNL->is_user_in_db($token, $dbname, $user);
        if (!$result['status']) {
            $this->CPNL->assign_db_user($token, $dbname, $user);
        }
    }

    /**
	 * MySQL connect database
	 *
	 * @return
	 */
	public function testConnectDatabase($dbhost, $user, $pass, $port)
	{
        /* ERR_DBCONNECT */
        $dbh = DUPX_DB::connect($dbhost, $user, $pass, null, $port);
        @mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
        ($dbh) or DUPX_Log::error(ERR_DBCONNECT . mysqli_connect_error());
        /* ERR_DBCSELECT */

        return $dbh;
    }
    
    /**
	 * MySQL connect database
	 *
	 * @return 
	 */
    public function simpleConnectDatabase($dbhost, $user, $pass, $dbname, $port)
    {
        $dbh = DUPX_DB::connect($dbhost, $user, $pass, $dbname, $port);
        return $dbh;
    }

    /**
	 * MySQL connect database
	 *
	 * @return 
	 */
    public function connectDatabase($dbhost, $user, $pass, $dbname, $dbport, $dbcharset, $dbcollatefb)
    {
        /* MYSQL CONNECTION */
        $dbh = DUPX_DB::connect($dbhost, $user, html_entity_decode($pass), $dbname, $dbport);
        @mysqli_character_set_name($dbh);
        @mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
        @mysqli_query($dbh, "SET max_allowed_packet = {$GLOBALS['DB_MAX_PACKETS']}");
        DUPX_DB::setCharset($dbh, $dbcharset, $dbcollatefb);

        return $dbh;
    }

    /**
	 * MySQL select database
	 *
	 * @return 
	 */
	public function selectDatabase($dbh, $dbname)
	{
        mysqli_select_db($dbh, $dbname) or DUPX_Log::error(sprintf(ERR_DBCREATE, $dbname));
    }
    
    /**
	 * MySQL connect database
	 *
	 * @return
	 */
	public function testRunDatabase($dbh, $dbcharset, $dbcollatefb)
	{
        @mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
        @mysqli_query($dbh, "SET max_allowed_packet = {$GLOBALS['DB_MAX_PACKETS']}");
        DUPX_DB::setCharset($dbh, $dbcharset, $dbcollatefb);
    }

	/**
	 * MySQL delete DB via Cpanel API
	 *
	 * @return 
	 */
	public function closeDatabase($dbh)
	{
        @mysqli_close($dbh);
    }

    /**
	 * MySQL scan SQL File
	 *
	 * @return
	 */
	public function scanSQLFile($dbh, $dbcharset, $dbcollatefb, $cur_root_path, $nbsp)
	{
        $root_path = DUPX_U::setSafePath($cur_root_path);
        @chmod($root_path, 0777);
        $sql_file_path = $root_path . '/database.sql';
        $sql_file_contents = file_get_contents($sql_file_path, true);
      
        /* ERROR: Reading database.sql file */
        if ($sql_file_contents === FALSE || strlen($sql_file_contents) < 10)
        {
            $msg = "<b>Unable to read the database.sql file from the archive.  Please check these items:</b> <br/>";
            $msg .= " - File: database.sql <br/> - Directory: [{$root_path}] <br/>";
            DUPX_Log::error($msg);
        }
    
        if ($nbsp) {
            /* Removes invalid space characters
            Complex Subject See: http://webcollab.sourceforge.net/unicode.html
            */
            DUPX_Log::info("NOTICE: Ran fix non-breaking space characters\n");
            $sql_file_contents = preg_replace('/\xC2\xA0/', ' ', $sql_file_contents);
        }
        
        /* Write new contents to install-data.sql */
        $new_sql_file_path	= "{$root_path}/{$GLOBALS['SQL_FILE_NAME']}";

        $new_sql_file_handle = fopen($new_sql_file_path, "w");
        $sql_file_copy_status = fwrite($new_sql_file_handle, $sql_file_contents);
        $sql_file_data	= explode(";\n", $sql_file_contents);
        $sql_file_length  = count($sql_file_data);
        $sql_file = null;
        $db_collatefb_log = '';
    
        if ($dbcollatefb) {
            $supportedCollations = DUPX_DB::getSupportedCollationsList($dbh);
            $collation_arr = array(
                'utf8mb4_unicode_520_ci',
                'utf8mb4_unicode_520',
                'utf8mb4_unicode_ci',
                'utf8mb4',
                'utf8_unicode_520_ci',
                'utf8_unicode_520',
                'utf8_unicode_ci',
                'utf8'
            );
            $latest_supported_collation = '';
            $latest_supported_index = -1;
    
            foreach ($collation_arr as $key => $val){
                if(in_array($val,$supportedCollations)){
                    $latest_supported_collation = $val;
                    $latest_supported_index = $key;
                    break;
                }
            }
    
            /* No need to replace if current DB is up to date */
            if($latest_supported_index != 0){
                for($i=0; $i < $latest_supported_index; $i++){
                    foreach ($sql_file_data as $index => $col_sql_query){
                        if(strpos($col_sql_query,$collation_arr[$i]) !== false){
                            $sql_file_data[$index] = str_replace($collation_arr[$i], $latest_supported_collation, $col_sql_query);
                            if(strpos($collation_arr[$i],'utf8mb4') !== false && strpos($latest_supported_collation,'utf8mb4') === false){
                                $sql_file_data[$index] = str_replace('utf8mb4','utf8',$sql_file_data[$index]);
                            }
                            $sub_query = str_replace("\n", '', substr($col_sql_query, 0, 75));
                            $db_collatefb_log .= "   - Collation '{$collation_arr[$i]}' set to '{$latest_supported_collation}' on query [{$sub_query}...]\n";
                        }
                    }
                }
            }
        }
    
        /* WARNING: Create installer-data.sql failed */
        if ($sql_file_copy_status === FALSE || filesize($sql_file_path) == 0 || !is_readable($sql_file_path))
        {
            $sql_file_size = DUPX_U::readableByteSize(filesize($sql_file_path));
            $msg  = "\nWARNING: Unable to properly copy database.sql ({$sql_file_size}) to {$new_sql_file_path}  Please check these items:\n";
            $msg .= "- Validate permissions and/or group-owner rights on database.sql and directory [{$root_path}] \n";
            DUPX_Log::info($msg);
        }

        /* WARNING: Create installer-data.sql failed */
        @mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
        @mysqli_query($dbh, "SET max_allowed_packet = {$GLOBALS['DB_MAX_PACKETS']}");
        DUPX_DB::setCharset($dbh, $dbcharset, $dbcollatefb);
    
        /*  Set defaults in-case the variable could not be read */
        $dbvar_maxtime		        = DUPX_DB::getVariable($dbh, 'wait_timeout');
        $dbvar_maxpacks		        = DUPX_DB::getVariable($dbh, 'max_allowed_packet');
        $dbvar_sqlmode		        = DUPX_DB::getVariable($dbh, 'sql_mode');
        $dbvar_maxtime		        = is_null($dbvar_maxtime) ? 300 : $dbvar_maxtime;
        $dbvar_maxpacks		        = is_null($dbvar_maxpacks) ? 1048576 : $dbvar_maxpacks;
        $dbvar_sqlmode		        = empty($dbvar_sqlmode) ? 'NOT_SET'  : $dbvar_sqlmode;
        $dbvar_version		        = DUPX_DB::getVersion($dbh);
        $sql_file_size1             = DUPX_U::readableByteSize(@filesize($sql_file_path));
        $sql_file_size2		        = DUPX_U::readableByteSize(@filesize($new_sql_file_path));
        $db_collatefb		        = isset($dbcollatefb) ? 'On' : 'Off';
        
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("DATABASE ENVIRONMENT");
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("MYSQL VERSION:\tThis Server: {$dbvar_version} -- Build Server: {$GLOBALS['FW_VERSION_DB']}");
        DUPX_Log::info("FILE SIZE:\tdatabase.sql ({$sql_file_size1}) - installer-data.sql ({$sql_file_size2})");
        DUPX_Log::info("TIMEOUT:\t{$dbvar_maxtime}");
        DUPX_Log::info("MAXPACK:\t{$dbvar_maxpacks}");
        DUPX_Log::info("SQLMODE:\t{$dbvar_sqlmode}");
        DUPX_Log::info("NEW SQL FILE:\t[{$new_sql_file_path}]");
        DUPX_Log::info("COLLATE RESET:\t{$db_collatefb}\n{$db_collatefb_log}");
    
        if ($qry_session_custom == false) {
            DUPX_Log::info("\n{$log}\n");
        }

        $result = array('status' => $sql_file_copy_status, 'data' => $sql_file_data, 'length' => $sql_file_length, 'maxpacks' => $dbvar_maxpacks);
        return $result;
    }

    /**
	 * MySQL create SQL file
	 *
	 *
	 * @return 
	 */
	public function writeSQLData($dbhost, $user, $pass, $dbname, $port, $dbh, $scan_result, $charset, $dbcollatefb)
	{
        /* WRITE DATA */
        DUPX_Log::info("--------------------------------------");
        DUPX_Log::info("DATABASE RESULTS");
        DUPX_Log::info("--------------------------------------");
        $fcgi_buffer_pool = 5000;
        $fcgi_buffer_count = 0;
        $dbquery_rows = 0;
        $dbtable_rows = 1;
        $dbquery_errs = 0;
        $counter = 0;
        @mysqli_autocommit($dbh, false);
        
        if (isset($scan_result)) {
            if (is_array($scan_result)) {
                while ($counter < $scan_result['length']) {
                    $query_strlen = strlen(trim($scan_result['data'][$counter]));
                    if ($scan_result['maxpacks'] < $query_strlen) {
                        DUPX_Log::info("**ERROR** Query size limit [length={$query_strlen}] [sql=" . substr($scan_result['data'][$counter], 0, 75) . "...]");
                        $dbquery_errs++;
                    } elseif ($query_strlen > 0) {
                        @mysqli_free_result(@mysqli_query($dbh, ($scan_result['data'][$counter])));
                        $err = mysqli_error($dbh);
        
                        /* Check to make sure the connection is alive */
                        if (!empty($err)) {
                            if (!mysqli_ping($dbh)) {
                                $this->closeDatabase($dbh);
                                $dbh = $this->simpleConnectDatabase($dbhost, $user, $pass, $dbname, $port);
                                // Reset session setup
                                @mysqli_query($dbh, "SET wait_timeout = {$GLOBALS['DB_MAX_TIME']}");
                                DUPX_DB::setCharset($dbh, $dbcharset, $dbcollatefb);
                            }
                            DUPX_Log::info("**ERROR** database error write '{$err}' - [sql=" . substr($scan_result['data'][$counter], 0, 75) . "...]");
                            $dbquery_errs++;
                        /* Buffer data to browser to keep connection open */
                        } else {
                            if ($GLOBALS['DB_FCGI_FLUSH'] && $fcgi_buffer_count++ > $fcgi_buffer_pool) {
                                $fcgi_buffer_count = 0;
                                DUPX_U::fcgiFlush();
                            }
                            $dbquery_rows++;
                        }
                    }
                    $counter++;
                }
                @mysqli_commit($dbh);
                @mysqli_autocommit($dbh, true);
        
                DUPX_Log::info("ERRORS FOUND:\t{$dbquery_errs}");
                DUPX_Log::info("TABLES DROPPED:\t{$drop_log}");
                DUPX_Log::info("QUERIES RAN:\t{$dbquery_rows}\n");
            }
        }
        return $dbh;
    }

    /**
	 * MySQL find the rows and tables to replace
	 *
	 * @return
	 */
	public function findRowsAndTablesToReplace($dbh, $fullsearch)
	{
        $result_tables = null;
        if ($fullsearch) {
            $result_tables= array();
        }
        $dbtable_count = 0;
        if ($result = mysqli_query($dbh, "SHOW TABLES")) {
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $table_rows = DUPX_DB::countTableRows($dbh, $row[0]);
                $dbtable_rows += $table_rows;
                if ($fullsearch) {
                    if (is_array($result_tables)) {
                        array_push($result_tables, $row[0]);
                    } else {
                        $result_tables = array($row[0]);
                    }
                }
                DUPX_Log::info("{$row[0]}: ({$table_rows})");
                $dbtable_count++;
            }
            @mysqli_free_result($result);
        }

        if ($dbtable_count == 0) {
            DUPX_Log::error("No tables where created during step 2 of the install.  Please review the <a href='{$GLOBALS['CURRENT_ROOT_PATH']}/installer-log.txt' target='install_log'>installer-log.txt</a> file for
                ERROR messages.  You may have to manually run the installer-data.sql with a tool like phpmyadmin to validate the data input.  If you have enabled compatibility mode
                during the package creation process then the database server version your using may not be compatible with this script.\n");
        }

        return $result_tables;
    }
    
    /**
	 * MySQL find the rows and tables to remove
	 *
	 * @return
	 */
	public function findRowsAndTablesToRemove($dbh, $dbprefix)
	{
        /***
         **  DATA CLEANUP: Perform Transient Cache Cleanup
         *  Remove all duplicator entries and record this one since this is a new install.
         */
        $dbdelete_count = 0;
        @mysqli_query($dbh, "DELETE FROM `{$dbprefix}duplicator_packages`");
        $dbdelete_count1 = @mysqli_affected_rows($dbh) or 0;
        @mysqli_query($dbh, "DELETE FROM `{$dbprefix}options` WHERE `option_name` LIKE ('_transient%') OR `option_name` LIKE ('_site_transient%')");
        $dbdelete_count2 = @mysqli_affected_rows($dbh) or 0;
        $dbdelete_count = (abs($dbdelete_count1) + abs($dbdelete_count2));
        DUPX_Log::info("\nRemoved '{$dbdelete_count}' cache/transient rows");
        
        /* Reset Duplicator Options */
        foreach ($GLOBALS['FW_OPTS_DELETE'] as $value) {
            mysqli_query($dbh, "DELETE FROM `{$dbprefix}options` WHERE `option_name` = '{$value}'");
        }
    }
    
    /**
	 * MySQL connect database
	 *
	 * @return 
	 */
    public function getBlogName($dbh, $blogname)
    {
        $blogname_result = mysqli_real_escape_string($dbh, $blogname);
        return $blogname_result;
    }
    
    /**
	 * MySQL find the rows and tables to remove
	 *
	 *
	 * @return
	 */
	public function printInstallLog($dbh, $dbprefix, $tables, $plugins)
	{
        $date = @date('h:i:s');
        $charset_client = @mysqli_character_set_name($dbh);
        $charset_server = @mysqli_character_set_name($dbh);
        $log = <<<LOG
\n\n********************************************************************************
* DUPLICATOR-LITE: INSTALL-LOG
* STEP-3 START @ {$date}
* NOTICE: Do NOT post to public sites or forums
********************************************************************************
CHARSET SERVER:\t{$charset_server}
CHARSET CLIENT:\t{$charset_client}
LOG;
        DUPX_Log::info($log);
        
        //Detailed logging
        $log  = "--------------------------------------\n";
        $log .= "POST DATA\n";
        $log .= "--------------------------------------\n";
        //$log .= print_r($POST_LOG, true);		
        $log .= "--------------------------------------\n";
        $log .= "SCANNED TABLES\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($tables) && count($tables > 0)) 
                ? print_r($tables, true) 
                : 'No tables selected to update';
        $log .= "\n--------------------------------------\n";
        $log .= "KEEP PLUGINS ACTIVE\n";
        $log .= "--------------------------------------\n";
        $log .= (isset($plugins) && count($plugins > 0)) 
                ? print_r($plugins, true) 
                : 'No plugins selected for activation';
        DUPX_Log::info($log);
        
        //UPDATE SETTINGS
        $blog_name   = $_POST['blogname'];
        $plugin_list = (isset($plugins)) ? $plugins : array();
        // Force Duplicator active so we the security cleanup will be available
        if (!in_array('duplicator/duplicator.php', $plugin_list)) {
            $plugin_list[] = 'duplicator/duplicator.php';
        }
        $serial_plugin_list	 = @serialize($plugin_list);
        
        mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$blog_name}' WHERE option_name = 'blogname' ");
        //mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$serial_plugin_list}'  WHERE option_name = 'active_plugins' ");
        
        $log  = "--------------------------------------\n";
        $log .= "SERIALIZER ENGINE\n";
        $log .= "[*] scan every column\n";
        $log .= "[~] scan only text columns\n";
        $log .= "[^] no searchable columns\n";
        $log .= "--------------------------------------";
        DUPX_Log::info($log);
    }

     /**
	 * MySQL find replace DB fields
	 *
	 * @return
	 */
    public function updateSettings($dbh, $dbprefix, $blogname, $plugins)
    {
        //UPDATE SETTINGS
        $plugin_list = (isset($plugins)) ? $plugins : array();
        // Force Duplicator active so we the security cleanup will be available
        if (!in_array('duplicator/duplicator.php', $plugin_list)) {
            $plugin_list[] = 'duplicator/duplicator.php';
        }
        $serial_plugin_list	 = @serialize($plugin_list);

        mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$blog_name}' WHERE option_name = 'blogname' ");
        //mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$serial_plugin_list}'  WHERE option_name = 'active_plugins' ");

        $log  = "--------------------------------------\n";
        $log .= "SERIALIZER ENGINE\n";
        $log .= "[*] scan every column\n";
        $log .= "[~] scan only text columns\n";
        $log .= "[^] no searchable columns\n";
        $log .= "--------------------------------------";
        DUPX_Log::info($log);
    }

     /**
	 * MySQL replace the DB fields
	 *
	 * @param string    $blog_name           The site blog name
	 * @param string    $url_new             The new site url
	 * @param string    $url_old             The old site url
	 * @param string    $siteurl             The site url(DB)
	 * @param string    $exe_safe_mode       The excution safe mode
	 *
	 * @return
	 */
    
    public function replaceDBField($dbh, $dbprefix, $blog_name, $url_new, $url_old, $path_new, $path_old, $siteurl, $tables, $fullsearch, $exe_safe_mode)
    {
        $url_old_json = str_replace('"', "", json_encode($url_old));
        $url_new_json = str_replace('"', "", json_encode($url_new));
        $path_old_json = str_replace('"', "", json_encode($path_old));
        $path_new_json = str_replace('"', "", json_encode($_POST['path_new']));

        //DIRS PATHS
        array_push($GLOBALS['REPLACE_LIST'],
            array('search' => $path_old,			            'replace' => $path_new),
            array('search' => $path_old_json,				    'replace' => $path_new_json),
            array('search' => urlencode($path_old),             'replace' => urlencode($path_new)),
            array('search' => rtrim(DUPX_U::unsetSafePath($path_old), '\\'), 'replace' => rtrim($path_new, '/'))
        );

        //SEARCH WITH NO PROTOCAL: RAW "//"
        $url_old_raw = str_ireplace(array('http://', 'https://'), '//', $url_old);
        $url_new_raw = str_ireplace(array('http://', 'https://'), '//', $url_new);
        $url_old_raw_json = str_replace('"',  "", json_encode($url_old_raw));
        $url_new_raw_json = str_replace('"',  "", json_encode($url_new_raw));
        array_push($GLOBALS['REPLACE_LIST'],
            //RAW
            array('search' => $url_old_raw,			 	'replace' => $url_new_raw),
            array('search' => $url_old_raw_json,		'replace' => $url_new_raw_json),
            array('search' => urlencode($url_old_raw), 	'replace' => urlencode($url_new_raw))
        );

        //SEARCH HTTP(S) EXPLICIT REQUEST
        //Because the raw replace above has already changed all urls just fix https/http issue
        //if the user has explicitly asked other-wise word boundary issues will occur:
        //Old site: http://mydomain.com/somename/
        //New site: http://mydomain.com/somename-dup/
        //Result: http://mydomain.com/somename-dup-dup/
        if (stristr($url_old, 'http:') && stristr($url_new, 'https:') ) {
            $url_old_http = str_ireplace('https:', 'http:', $url_new);
            $url_new_http = $url_new;
            $url_old_http_json = str_replace('"',  "", json_encode($url_old_http));
            $url_new_http_json = str_replace('"',  "", json_encode($url_new_http));

        } elseif(stristr($url_old, 'https:') && stristr($url_new, 'http:')) {
            $url_old_http = str_ireplace('http:', 'https:', $url_new);
            $url_new_http = $url_new;
            $url_old_http_json = str_replace('"',  "", json_encode($url_old_http));
            $url_new_http_json = str_replace('"',  "", json_encode($url_new_http));
        }
        if(isset($url_old_http)){
            array_push($GLOBALS['REPLACE_LIST'],
                array('search' => $url_old_http,			 	 'replace' => $url_new_http),
                array('search' => $url_old_http_json,			 'replace' => $url_new_http_json),
                array('search' => urlencode($url_old_http),  	 'replace' => urlencode($url_new_http))
            );
        }

        //Remove trailing slashes
        function _dupx_array_rtrim(&$value) {
            $value = rtrim($value, '\/');
        }
        array_walk_recursive($GLOBALS['REPLACE_LIST'], _dupx_array_rtrim);

        @mysqli_autocommit($dbh, false);

        $report = DUPX_UpdateEngine::load($dbh, $GLOBALS['REPLACE_LIST'], $tables, $fullsearch);
        @mysqli_commit($dbh);
        @mysqli_autocommit($dbh, true);

        DUPX_UpdateEngine::logStats($report);
        DUPX_UpdateEngine::logErrors($report);

        /* Reset the postguid data */
        if ($_POST['postguid']) {
            mysqli_query($dbh, "UPDATE `{$dbprefix}posts` SET guid = REPLACE(guid, '{$url_new}', '{$url_old})");
            $update_guid = @mysqli_affected_rows($dbh) or 0;
            DUPX_Log::info("Reverted '{$update_guid}' post guid columns back to '{$url_old}'");
        }
        
        /** FINAL UPDATES: Must happen after the global replace to prevent double pathing
         http://xyz.com/abc01 will become http://xyz.com/abc0101  with trailing data */
        mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$url_new}'  WHERE option_name = 'home' ");
        mysqli_query($dbh, "UPDATE `{$dbprefix}options` SET option_value = '{$siteurl}'  WHERE option_name = 'siteurl' ");
        mysqli_query($dbh, "INSERT INTO `{$dbprefix}options` (option_value, option_name) VALUES('{$exe_safe_mode}','duplicator_exe_safe_mode')");
    }
    
     /**
	 * MySQL create new WP user
	 *
	 * @param string    $wp_user    The WP user name
	 * @param string    $wp_pass    The WP user password
	 *
	 * @return
	 */
    public function createNewWordpressAdminUser($dbh, $dbprefix, $wp_user, $wp_pass)
    {    
        //===============================================
        //GENERAL UPDATES & CLEANUP
        //===============================================
        DUPX_Log::info("\n====================================");
        DUPX_Log::info('GENERAL UPDATES & CLEANUP:');
        DUPX_Log::info("====================================\n");

        $newuser_check = @mysqli_query($dbh, "SELECT COUNT(*) AS count FROM `{$dbprefix}users` WHERE user_login = '{$wp_user}' ");
        $newuser_row = null;
        if ($newuser_check) {
            $newuser_row   = mysqli_fetch_row($newuser_check);
        }
        $newuser_count = is_null($newuser_row) ? 0 : $newuser_row[0];
        
        if ($newuser_count == 0) {
        
            $newuser_datetime =	@date("Y-m-d H:i:s");
            $newuser_security = mysqli_real_escape_string($dbh, 'a:1:{s:13:"administrator";s:1:"1";}');
    
            $newuser_test1 = @mysqli_query($dbh, "INSERT INTO `{$dbprefix}users` 
                (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) 
                VALUES ('{$wp_user}', MD5('{$wp_pass}'), '{$wp_user}', '', '{$newuser_datetime}', '', '0', '{$wp_user}')");
    
            $newuser_insert_id = mysqli_insert_id($dbh);
    
            $newuser_test2 = @mysqli_query($dbh, "INSERT INTO `{$dbprefix}usermeta` 
                    (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', '{$dbprefix}capabilities', '{$newuser_security}')");
    
            $newuser_test3 = @mysqli_query($dbh, "INSERT INTO `{$dbprefix}usermeta` 
                    (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', '{$dbprefix}user_level', '10')");
                    
            //Misc Meta-Data Settings:
            @mysqli_query($dbh, "INSERT INTO `{$dbprefix}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'rich_editing', 'true')");
            @mysqli_query($dbh, "INSERT INTO `{$dbprefix}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'admin_color',  'fresh')");
            @mysqli_query($dbh, "INSERT INTO `{$dbprefix}usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$newuser_insert_id}', 'nickname', '{$wp_user}')");
    
            if ($newuser_test1 && $newuser_test2 && $newuser_test3) {
                DUPX_Log::info("NEW WP-ADMIN USER: New username '{$wp_user}' was created successfully \n ");
            } else {
                $newuser_warnmsg = "NEW WP-ADMIN USER: Failed to create the user '{$wp_user}' \n ";
                DUPX_Log::info($newuser_warnmsg);
            }			
        } 
        else {
            $newuser_warnmsg = "NEW WP-ADMIN USER: Username '{$wp_user}' already exists in the database.  Unable to create new account \n";
            DUPX_Log::info($newuser_warnmsg);
        }
    }

    public function updateWPConfig($root_path, $url_new, $retain_config, $dbh) {
        //===============================================
        //CONFIGURATION FILE UPDATES
        //===============================================
        DUPX_Log::info("\n====================================");
        DUPX_Log::info('CONFIGURATION FILE UPDATES:');
        DUPX_Log::info("====================================\n");
        DUPX_WPConfig::updateStandard();
        $config_file = DUPX_WPConfig::updateExtended();
        DUPX_Log::info("UPDATED WP-CONFIG: {$root_path}/wp-config.php' (if present)");

        //Web Server Config Updates
        if (!isset($url_new) || $retain_config) {
            DUPX_Log::info("\nNOTICE: Manual update of permalinks required see:  Admin > Settings > Permalinks > Click Save Changes");
            DUPX_Log::info("Retaining the original htaccess, user.ini or web.config files may cause issues with the setup of this site.");
            DUPX_Log::info("If you run into issues during or after the install process please uncheck the 'Config Files' checkbox labeled:");
            DUPX_Log::info("'Retain original .htaccess, .user.ini and web.config' from Step 1 and re-run the installer. Backups of the");
            DUPX_Log::info("orginal config files will be made and can be merged per required directive.");
        } else {
            DUPX_ServerConfig::setup($dbh);
        }
        return $config_file;
    }

     /**
	 * MySQL update the multi
	 *	 *
	 * @return
	 */
    public function updateMU($dbh, $dbprefix, $url_new, $url_old)
    {
        /** ==============================
         * MU Updates*/
        $mu_newDomain       = parse_url($_POST['url_new']);
        $mu_oldDomain       = parse_url($_POST['url_old']);
        $mu_newDomainHost   = $mu_newDomain['host'];
        $mu_oldDomainHost   = $mu_oldDomain['host'];
        $mu_newUrlPath      = parse_url($_POST['url_new'], PHP_URL_PATH);
        $mu_oldUrlPath      = parse_url($_POST['url_old'], PHP_URL_PATH);

        //Force a path for PATH_CURRENT_SITE
        $mu_newUrlPath      = (empty($mu_newUrlPath) || ($mu_newUrlPath == '/')) ? '/'  : rtrim($mu_newUrlPath, '/') . '/';
        $mu_oldUrlPath      = (empty($mu_oldUrlPath) || ($mu_oldUrlPath == '/')) ? '/'  : rtrim($mu_oldUrlPath, '/') . '/';

        $mu_updates = @mysqli_query($dbh, "UPDATE `{$dbprefix}blogs` SET domain = '{$mu_newDomainHost}' WHERE domain = '{$mu_oldDomainHost}'");
        if ($mu_updates) {
            DUPX_Log::info("Update MU table blogs: domain {$mu_newDomainHost} ");
            DUPX_Log::info("UPDATE `{$dbprefix}blogs` SET domain = '{$mu_newDomainHost}' WHERE domain = '{$mu_oldDomainHost}'");
        }
    }

     /**
	 * MySQL test finally
	 *	 *
	 * @return
	 */    
    public function finalTest($dbh, $dbprefix, $config_file) {        
        //===============================================
        //NOTICES TESTS
        //===============================================
        DUPX_Log::info("\n====================================");
        DUPX_Log::info("NOTICES");
        DUPX_Log::info("====================================\n");
        $config_vars = array('WPCACHEHOME', 'COOKIE_DOMAIN', 'WP_SITEURL', 'WP_HOME', 'WP_TEMP_DIR');
        $config_found = DUPX_U::getListValues($config_vars, $config_file);

        //Config File:
        if (! empty($config_found)) {
            $msg  = "NOTICE: The wp-config.php has the following values set [" . implode(", ", $config_found) . "]. \n";
            $msg .= 'Please validate these values are correct in your wp-config.php file.  See the codex link for more details: https://codex.wordpress.org/Editing_wp-config.php';
            DUPX_Log::info($msg);
        }

        //Database: 
        $result = @mysqli_query($closeDatabase, "SELECT option_value FROM `{$dbprefix}options` WHERE option_name IN ('upload_url_path','upload_path')");
        if ($result) {
            while ($row = mysqli_fetch_row($result)) {
                if (strlen($row[0])) {
                    $msg  = "NOTICE: The media settings values in the table '{$dbprefix}options' has at least one the following values ['upload_url_path','upload_path'] set. \n";
                    $msg .= "Please validate these settings by logging into your wp-admin and going to Settings->Media area and validating the 'Uploading Files' section";
                    DUPX_Log::info($msg);
                    break;
                }
            }
        }

      //  $this->closeDatabase($dbh);

        $ajax2_end = DUPX_U::getMicrotime();
        $ajax2_sum = DUPX_U::elapsedTime($ajax2_end, $ajax2_start);
        DUPX_Log::info("\nSTEP 3 COMPLETE @ " . @date('h:i:s') . " - RUNTIME: {$ajax2_sum}\n\n");

    }
}