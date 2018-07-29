<?php

require_once('dupx/class.dupx.u.php');
require_once('dupx/class.dupx.log.php');
require_once('dupx/class.dupx.serverconfig.php');

/* It open the log file and set the file handle as a global variable. */
$GLOBALS['LOG_FILE_HANDLE']                 = @fopen($GLOBALS['LOG_FILE_PATH'], "w+");

//PAGE VARS
$php_max_time                       = @ini_get("max_execution_time");
$php_max_time                       = ($php_max_time == 0) ? "[0] time limit restriction disabled" : "[{$php_max_time}] time limit restriction enabled";
$root_path                          = DUPX_U::setSafePath($GLOBALS['CURRENT_ROOT_PATH']);
$package_path                       = "{$root_path}/{$_POST['archive_name']}";
$ajax1_start                        = DUPX_U::getMicrotime();
$zip_support                        = class_exists('ZipArchive') ? 'Enabled' : 'Not Enabled';

$GLOBALS['CHOWN_ROOT_PATH']             = @chmod("{$root_path}", 0755);
$GLOBALS['CHOWN_LOG_PATH']              = @chmod("{$root_path}/{$GLOBALS['LOG_FILE_NAME']}", 0644);

/*********  
 *******
  ****                   The main process start!
  */

/*****
  ***   The first process
  **    This process log the current status.
  */
DUPX_Log::info("\n>>> START EXTRACTION:");
DUPX_Log::info("********************************************************************************");
DUPX_Log::info('* DUPLICATOR-LITE: INSTALL-LOG');
DUPX_Log::info("* VERSION: {$GLOBALS['FW_DUPLICATOR_VERSION']}");
DUPX_Log::info('* STEP-1 START @ '.@date('h:i:s'));
DUPX_Log::info('* NOTICE: Do NOT post this data to public sites or forums');
DUPX_Log::info("********************************************************************************");
DUPX_Log::info("PHP VERSION:\t".phpversion().' | SAPI: '.php_sapi_name());
DUPX_Log::info("PHP TIME LIMIT:\t{$php_max_time}");
DUPX_Log::info("PHP MEMORY:\t".$GLOBALS['PHP_MEMORY_LIMIT'].' | SUHOSIN: '.$GLOBALS['PHP_SUHOSIN_ON']);
DUPX_Log::info("SERVER:\t\t{$_SERVER['SERVER_SOFTWARE']}");
DUPX_Log::info("DOC ROOT:\t{$root_path}");
DUPX_Log::info("DOC ROOT 755:\t".var_export($GLOBALS['CHOWN_ROOT_PATH'], true));
DUPX_Log::info("LOG FILE 644:\t".var_export($GLOBALS['CHOWN_LOG_PATH'], true));
DUPX_Log::info("REQUEST URL:\t{$GLOBALS['URL_PATH']}");
DUPX_Log::info("SAFE MODE :\t{$_POST['exe_safe_mode']}");

$log = "--------------------------------------\n";
$log .= "POST DATA\n";
$log .= "--------------------------------------\n";
$log .= print_r($POST_LOG, true);
DUPX_Log::info($log, 2);

$log = "--------------------------------------\n";
$log .= "ARCHIVE EXTRACTION\n";
$log .= "--------------------------------------\n";
$log .= "NAME:\t{$_POST['archive_name']}\n";
$log .= "SIZE:\t".DUPX_U::readableByteSize(@filesize($_POST['archive_name']))."\n";
$log .= "ZIP:\t{$zip_support} (ZipArchive Support)";
DUPX_Log::info($log);

if ($GLOBALS['FW_PACKAGE_NAME'] != $_POST['archive_name']) {
    $log = "\n--------------------------------------\n";
    $log .= "WARNING: This package set may be incompatible!  \nBelow is a summary of the package this installer was built with and the package used. \n";
    $log .= "To guarantee accuracy the installer and archive should match. For details see the online FAQs.";
    $log .= "\nCREATED WITH:\t{$GLOBALS['FW_PACKAGE_NAME']} \nPROCESSED WITH:\t{$_POST['archive_name']}  \n";
    $log .= "--------------------------------------\n";
    DUPX_Log::info($log);
}

/*
 *   RESET SERVER CONFIG FILES
 */
if ($_POST['retain_config']) {
	DUPX_Log::info("\nNOTICE: Manual update of permalinks required see:  Admin > Settings > Permalinks > Click Save Changes");
	DUPX_Log::info("Retaining the original htaccess, user.ini or web.config files may cause issues with the setup of this site.");
	DUPX_Log::info("If you run into issues during or after the install process please uncheck the 'Config Files' checkbox labeled:");
	DUPX_Log::info("'Retain original .htaccess, .user.ini and web.config' from Step 1 and re-run the installer. Backups of the");
	DUPX_Log::info("orginal config files will be made and can be merged per required directive.");
} else {
	DUPX_ServerConfig::reset();
}

/*
 *   FINAL RESULTS
 */
$ajax1_end	 = DUPX_U::getMicrotime();
$ajax1_sum	 = DUPX_U::elapsedTime($ajax1_end, $ajax1_start);
DUPX_Log::info("\nSTEP-1 COMPLETE @ " . @date('h:i:s') . " - RUNTIME: {$ajax1_sum}");

//PAGE VARS
$date_time      = @date('h:i:s');
$ajax2_start	= DUPX_U::getMicrotime();

function_exists('mysqli_connect') or DUPX_Log::error(ERR_MYSQLI_SUPPORT);
