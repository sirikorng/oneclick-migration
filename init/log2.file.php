<?php

require_once('dupx/class.dupx.u.php');
require_once('dupx/class.dupx.log.php');

/*****
  ***   The second process
  **    This process log the current status.
  */
$log = <<<LOG
\n\n********************************************************************************
* DUPLICATOR-LITE: INSTALL-LOG
* STEP-2 START @ {$date_time}
* NOTICE: Do NOT post to public sites or forums
************************************************************************************
LOG;
DUPX_Log::info($log);

$log  = "--------------------------------------\n";
$log .= "POST DATA\n";
$log .= "--------------------------------------\n";
$log .= print_r($POST_LOG, true);
DUPX_Log::info($log, 2);

/* Scan the database file and create the installer-data.sql. */
$log = '';
$faq_url = $GLOBALS['FAQ_URL'];
$utm_prefix = '?utm_source=duplicator_free&utm_medium=wordpress_plugin&utm_campaign=problem_resolution&utm_content=';
$sql_file_name = 'database.sql';
$db_file_size = filesize($GLOBALS['CURRENT_ROOT_PATH'] . '/' . $sql_file_name);
$php_mem = $GLOBALS['PHP_MEMORY_LIMIT'];
$php_mem_range = DUPX_U::getBytes($GLOBALS['PHP_MEMORY_LIMIT']);
$php_mem_range = $php_mem_range == null ?  0 : $php_mem_range - 5000000; //5 MB Buffer

/*
  Fatal Memory errors from file_get_contents is not catchable.
  Try to warn ahead of time with a buffer in memory differenceble.
 */
if ($db_file_size >= $php_mem_range  && $php_mem_range != 0)
{
  $db_file_size = DUPX_U::readableByteSize($db_file_size);
  $msg = "\nWARNING: The database script is '{$db_file_size}' in size.  The PHP memory allocation is set\n";
  $msg .= "at '{$php_mem}'.  There is a high possibility that the installer script will fail with\n";
  $msg .= "a memory allocation error when trying to load the database.sql file.  It is\n";
  $msg .= "recommended to increase the 'memory_limit' setting in the php.ini config file.\n";
    $msg .= "see: {$faq_url}{$utm_prefix}inst_step2_lgdbscript#faq-trouble-056-q \n";
  DUPX_Log::info($msg);
}