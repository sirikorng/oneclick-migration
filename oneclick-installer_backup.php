<?php

/** Absolute path to the Installer directory. - necessary for php protection */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');

/* Some machines don’t have this set so just do it here. */
date_default_timezone_set('UTC');

////
///$_POST['wp_user'] = 'mtest1';

/*
 * The global Data 1.
  *
  * This data is necessary to call the function without the parameters.
  */

/* These data are default data when the input data is none. */
$GLOBALS['DBHOST_DEFAULT']              = 'localhost';
$GLOBALS['DBPORT_DEFAULT']              = 3306;
$GLOBALS['DBUSER_PREFIX_DEFAULT']       = 'thailay0_';
$GLOBALS['DBUSER_DEFAULT']              = 'shop';
$GLOBALS['DBPASS_DEFAULT']              = '!jpassword';
$GLOBALS['DBNAME_PREFIX_DEFAULT']       = 'thailay0_';
$GLOBALS['SITE_DIR_DEFAULT']            = '/home1/thailay0/public_html/yourlogo';
$GLOBALS['INSTALL_DIR_PREFIX_DEFAULT']  = 'shop';
$GLOBALS['PACKAGE_NAME_DEFAULT']        = '/home1/thailay0/public_html/yourlogo/installer/demo_archive.zip';
$GLOBALS['CPANEL_HOST_DEFAULT']         = 'https://yourlogo.online:2083';
$GLOBALS['CPANEL_USER_DEFAULT']         = 'thailay0';
$GLOBALS['CPANEL_PASS_DEFAULT']         = 'Pare4322@7';
$GLOBALS['URL_NEW_DEFAULT']             = 'http://yourlogo.online';
$GLOBALS['URL_OLD_DEFAULT']             = 'http://yourlogo.site';
$GLOBALS['PATH_NEW_DEFAULT']            = 'http://yourlogo.online';
$GLOBALS['PATH_OLD_DEFAULT']            = 'http://yourlogo.site';
$GLOBALS['SITEURL_DEFAULT']             = 'http://yourlogo.online';

/* Log file and SQL file path */
$GLOBALS['SQL_FILE_NAME']               = "installer-data.sql";
$GLOBALS['LOG_FILE_NAME']               = "installer-log.txt";
$GLOBALS['LOGGING']                     = 1;

/* The property information of the defult site . */
$GLOBALS['FW_TABLEPREFIX']              = '44e_';
$GLOBALS['FW_URL_OLD']                  = 'http://yourlogo.site';
$GLOBALS['FW_WPROOT']                   = '/home1/thailay0/public_html/yourlogo_site/';
$GLOBALS['FW_WPLOGIN_URL']              = 'http://yourlogo.site/wp-login.php';
$GLOBALS['FW_OPTS_DELETE']              = json_decode('["duplicator_ui_view_state","duplicator_package_active","duplicator_settings"]', true);
$GLOBALS['REPLACE_LIST'] = array();

/* DATABASE SETUP: all time in seconds */
$GLOBALS['DBCHARSET_DEFAULT']           = 'utf8';
$GLOBALS['DBCOLLATEFB_DEFAULT']         = 'utf8_general_ci';
$GLOBALS['FAQ_URL']                     = 'https://snapcreek.com/duplicator/docs/faqs-tech';
$GLOBALS['DB_MAX_TIME']                 = 5000;
$GLOBALS['DB_MAX_PACKETS']              = 268435456;
$GLOBALS['DB_FCGI_FLUSH']               = false;
ini_set('mysql.connect_timeout', '5000');

/* PHP SETUP: all time in seconds */
ini_set('memory_limit', '512M');
ini_set("max_execution_time", '5000');
ini_set("max_input_time", '5000');
ini_set('default_socket_timeout', '5000');
@set_time_limit(0);


/**  
 **  If the admin user of the new sub site is none, the program will exit.
  *  This data is necessary to call the function without the parameters.
  */
if (!isset($_POST['wp_user'])) {
    die();
}

/******  
 *****  The posted Data.
  ***  This data is posted the caller is submited in form.
  */
$_POST['sitedir']                       = '/home1/thailay0/public_html/yourlogo/';
$_POST['fullsearch']                    = 1;

$_POST['cpanel_host']                   = isset($_POST['cpanel_host']) ? trim($_POST['cpanel_host']) : $GLOBALS['CPANEL_HOST_DEFAULT'];
$_POST['cpanel_user']                   = isset($_POST['cpanel_user']) ? trim($_POST['cpanel_user']) : $GLOBALS['CPANEL_USER_DEFAULT'];
$_POST['cpanel_pass']                   = isset($_POST['cpanel_pass']) ? trim($_POST['cpanel_pass']) : $GLOBALS['CPANEL_PASS_DEFAULT'];

$_POST['wp_pass']                       = isset($_POST['wp_pass']) ? trim($_POST['wp_pass']) : $_POST['wp_user'];

$_POST['dbhost']                        = isset($_POST['dbhost']) ? trim($_POST['dbhost']) : $GLOBALS['DBHOST_DEFAULT'];
$_POST['dbport']                        = isset($_POST['dbport']) ? trim($_POST['dbport']) : $GLOBALS['DBPORT_DEFAULT'] ;
$_POST['dbuser']                        = isset($_POST['dbuser']) ? $GLOBALS['DBUSER_PREFIX_DEFAULT'] . trim($_POST['dbuser']) : $GLOBALS['DBUSER_PREFIX_DEFAULT'] . $GLOBALS['DBUSER_DEFAULT'];
$_POST['dbpass']                        = isset($_POST['dbpass']) ? trim($_POST['dbpass']) : $GLOBALS['DBPASS_DEFAULT'];
$_POST['dbname']                        = isset($_POST['dbname']) ? $GLOBALS['DBNAME_PREFIX_DEFAULT'] . trim($_POST['dbname']) : $GLOBALS['DBNAME_PREFIX_DEFAULT'] . $_POST['wp_user'];
$_POST['dbcharset']                     = isset($_POST['dbcharset'])  ? trim($_POST['dbcharset']) : $GLOBALS['DBCHARSET_DEFAULT'];
$_POST['dbcollatefb']                   = isset($_POST['dbcollatefb']) ? $_POST['dbcollatefb'] : $GLOBALS['DBCOLLATEFB_DEFAULT'];
$_POST['dbprefix']                      = isset($_POST['dbprefix']) ? $_POST['dbprefix'] : $GLOBALS['FW_TABLEPREFIX'];

$_POST['archive_engine']                = isset($_POST['archive_engine']) ? $_POST['archive_engine']  : 'auto';
$_POST['archive_filetime']              = (isset($_POST['archive_filetime'])) ? $_POST['archive_filetime'] : 'current';
$_POST['exe_safe_mode']                 = (isset($_POST['exe_safe_mode'])) ? $_POST['exe_safe_mode'] : 0;
$_POST['dbaction']                      = isset($_POST['dbaction']) ? $_POST['dbaction'] : 'create';
$_POST['dbnbsp']                        = (isset($_POST['dbnbsp']) && $_POST['dbnbsp'] == '1') ? true : false;
$_POST['cache_wp']                      = (isset($_POST['cache_wp']))   ? true : false;
$_POST['cache_path']                    = (isset($_POST['cache_path'])) ? true : false;
$_POST['tables']                        = array();

$_POST['postguid']		                = isset($_POST['postguid']) && $_POST['postguid'] == 1 ? 1 : 0;
$_POST['path_old']		                = isset($_POST['path_old']) ? trim($_POST['path_old']) : $GLOBALS['PATH_OLD_DEFAULT'];
$_POST['path_new']		                = isset($_POST['path_new']) ? trim($_POST['path_new']) : $GLOBALS['PATH_NEW_DEFAULT'] . '/' . $GLOBALS['INSTALL_DIR_PREFIX'];
$_POST['siteurl']		                = isset($_POST['siteurl']) ? rtrim(trim($_POST['siteurl']), '/') : rtrim(trim($GLOBALS['SITEURL_DEFAULT'] . '/' . $GLOBALS['INSTALL_DIR_PREFIX'] . '/' . $_POST['wp_user']), '/');
$_POST['tables']		                = isset($_POST['tables']) && is_array($_POST['tables']) ? array_map('stripcslashes', $_POST['tables']) : array();
$_POST['url_old']		                = isset($_POST['url_old']) ? trim($_POST['url_old']) : $GLOBALS['URL_OLD_DEFAULT'];
$_POST['url_new']		                = isset($_POST['url_new']) ? rtrim(trim($_POST['url_new']), '/') : $GLOBALS['URL_NEW_DEFAULT'];
$_POST['retain_config']                 = (isset($_POST['retain_config']) && $_POST['retain_config'] == '1') ? true : false;

/******  
 *****  The posted Data 2.
  ***
  ***   This data is posted the caller is submited in form.
  **    The difference from the posted data 1 is determinated after the posted is decided.
  */
$GLOBALS['INSTALL_DIR_PREFIX']          = isset($_POST['insdir']) ? trim($_POST['insdir']) : $GLOBALS['INSTALL_DIR_PREFIX_DEFAULT'];
$GLOBALS['SITE_DIR']                    = isset($_POST['sitedir']) ?  trim($_POST['sitedir']) : $GLOBALS['SITE_DIR_DEFAULT'];
$GLOBALS['CURRENT_ROOT_PATH']           = isset($_POST['wp_user']) ? $GLOBALS['SITE_DIR'] . '/' . $GLOBALS['INSTALL_DIR_PREFIX'] . '/' . $_POST['wp_user'] : dirname(__FILE__);
$GLOBALS['PHP_MEMORY_LIMIT']            = ini_get('memory_limit') === false ? 'n/a' : ini_get('memory_limit');
$GLOBALS['PHP_SUHOSIN_ON']              = extension_loaded('suhosin') ? 'enabled' : 'disabled';
$GLOBALS['FW_PACKAGE_NAME']             = isset($_POST['archive']) ?  trim($_POST['archive']) : $GLOBALS['PACKAGE_NAME_DEFAULT'];
$GLOBALS['ARCHIVE_PATH']                = $GLOBALS['CURRENT_ROOT_PATH'] . '/' . $GLOBALS['FW_PACKAGE_NAME'];
$GLOBALS['ARCHIVE_PATH']                = str_replace("\\", "/", $GLOBALS['ARCHIVE_PATH']);
$GLOBALS['LOG_FILE_PATH']               = $GLOBALS['CURRENT_ROOT_PATH'] . '/' . $GLOBALS['LOG_FILE_NAME'];

$_POST['archive_name']                  = isset($_POST['archive_name']) ? $_POST['archive_name'] : $GLOBALS['PACKAGE_NAME_DEFAULT'];

/*** Include - File Migration ***/
require_once('migration/file.migration.php');

/* File Processing */
FileMigration::createDirectory($GLOBALS['CURRENT_ROOT_PATH']);
FileMigration::unzipFile($GLOBALS['CURRENT_ROOT_PATH'], $_POST['archive_name']);

/* Log File 1 */
require_once('init/log1.file.php');


/*** Include - Database Migration ***/
require_once('migration/database.migration.php');

/* Database Processing */
$db_mgr = new DatabaseMigration();

$db_mgr->setHostDBInfo($_POST['cpanel_host'], $_POST['cpanel_user'], $_POST['cpanel_pass'], $_POST['dbhost'], $_POST['dbname'], $_POST['dbuser'], $_POST['dbpass'], $_POST['dbport']);
$db_mgr->setDatabaseConfig($_POST['dbprefix'], $_POST['dbcharset'], $_POST['dbcollatefb']);

$db_mgr->connectHost();
$db_mgr->grantDatabaseUser();
$db_mgr->testConnectDatabase();
$db_mgr->selectDatabase();

/* Log File 2 */
require_once('init/log2.file.php');

$db_mgr->testRunDatabase();
$db_mgr->scanSQLFile();

$profile_start = DUPX_U::getMicrotime();

$db_mgr->writeSQLData();
$_POST['tables'] = $db_mgr->findRowsAndTablesToReplace($_POST['fullsearch']);
$db_mgr->findRowsAndTablesToRemove();

$db_mgr->closeDatabase();

/* Log File 3 */
require_once('init/log3.file.php');

$db_mgr->connectDatabase();

$_POST['blogname'] = $db_mgr->getBlogName($_POST['blogname']);
$db_mgr->printInstallLog($_POST['tables'], $_POST['plugins']);
$db_mgr->updateSettings($_POST['blogname'], $_POST['plugins']);
$db_mgr->findReplaceDBField($_POST['url_new'], $_POST['url_old'], $_POST['path_new'], $_POST['path_old'], $_POST['tables'], $_POST['fullsearch']);
$db_mgr->replaceDBField($_POST['blog_name'], $_POST['url_new'], $_POST['url_old'], $_POST['siteurl'], $_POST['exe_safe_mode'], $_POST['mu_newDomainHost'], $_POST['mu_oldDomainHost']);

FileMigration::updateWPConfig($GLOBALS['CURRENT_ROOT_PATH'], $_POST['archive_name']);

$db_mgr->createNewWPUser($_POST['wp_user'], $_POST['wp_pass']);
$db_mgr->updateMU();
$db_mgr->finalTest();

?>

<div class="row"  style="width: 50%;margin-left: 25%;margin-top: 5%;">
    <h1>The Shop has been successfully created!</h1>
    <form method="post" action="<?php echo $_POST['siteurl']?>/wp-login.php">
        <button type="submit" class="btn btn-success btn-lg">View Shop</button>
    </form>
</div>