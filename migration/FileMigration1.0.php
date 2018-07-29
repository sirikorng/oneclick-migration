<?php

/**
 * Include Classes
 **/

require_once('dupx/class.dupx.u.php');
require_once('dupx/class.dupx.log.php');
require_once('dupx/class.dupx.wpconfig.php');

/**
 * 
 *
 * Standard: 
 * @link 
 *
 * @package 
 *
 */
class FileMigration
{
	/**
	 * FILE create Directory
	 *
	 * @param string    $target     The target directory path
	 *
	 * @return
	 */
	public static function createDirectory($target)
	{        
        @mkdir($target);
        @chmod($target, 0755);
    }

	/**
	 * FILE delete directory
	 *
	 * @param string    $target     The target file path
	 *
	 * @return
	 */
	public static function deleteDirectory($target)
	{        
        if (@is_dir($target)) {
            @rmdir($target);
        }
    }

	/**
	 * FILE unzip the archive file
	 *
	 * @param string    $target     The target file path
	 * @param string    $archive    The archive file path
	 *
	 * @return
	 */
	public static function unzipFile($target, $archive)
	{
        $prev_log = "";
        if (class_exists('ZipArchive')) {
            $zip	 = new ZipArchive();
            if ($zip->open($archive) === TRUE) {
    
                if (!$zip->extractTo($target)) {
                    $prev_log .= 'Errors extracting zip file.  Portions or part of the zip archive did not extract correctly.    Try to extract the archive manually with a client side program like unzip/win-zip/winrar or your hosts cPanel to make sure the file is not corrupted.  If the file extracts correctly then there is an invalid file or directory that PHP is unable to extract.  This can happen if your moving from one operating system to another where certain naming conventions work on one environment and not another. <br/><br/> <b>Workarounds:</b> <br/> 1. Create a new package and be sure to exclude any directories that have invalid names or files in them.   This warning will be displayed on the scan results under "Name Checks". <br/> 2. Manually extract the zip file with a client side program or your hosts cPanel.  Then under options in step 1 of this installer check the "Manual Archive Extraction" option and perform the install.';
                }
                $log = print_r($zip, true);

                if ($_POST['archive_filetime'] == 'original') {
                    $log .= "File timestamp is 'Original' mode.\n";
                    for ($idx = 0; $s = $zip->statIndex($idx); $idx++) {
                        touch($target.DIRECTORY_SEPARATOR.$s['name'], $s['mtime']);
                    }
                } else {
                    $now = date("Y-m-d H:i:s");
                    $log .= "File timestamp is 'Current' mode: {$now}\n";
                }
    
                $close_response = $zip->close();
                $log .= "<<< EXTRACTION COMPLETE: " . var_export($close_response, true);
                $prev_log .= $log;
            } else {
                $prev_log = 'Failed to open zip archive file. Please be sure the archive is completely downloaded before running the installer. Try to extract the archive manually to make sure the file is not corrupted.';
            }
        } else {
            $log_file_handle = @fopen($GLOBALS['LOG_FILE_NAME'] , "w+");
            $msg = "ZipArchive class do not exist";
            $breaks = array("<br />","<br>","<br/>");  
            $log_msg = str_ireplace($breaks, "\r\n", $msg);
            $log_msg = strip_tags($log_msg);
            @fwrite($log_file_handle, "\nINSTALLER ERROR:\n{$log_msg}\n");
            @fclose($log_file_handle);
            die("<div class='dupx-ui-error'><hr size='1' /><b style='color:#B80000;'>INSTALL ERROR!</b><br/>{$msg}</div>");
        }
    }

    /**
	 * FILE  update WP Config file
	 *
	 * @param string    root_path   The WP root file path
	 *
	 * @return
	 */
    public static function updateWPConfig($root_path)
    {
        DUPX_WPConfig::updateStandard();
        $config_file = DUPX_WPConfig::updateExtended();
        DUPX_Log::info("UPDATED WP-CONFIG: {$root_path}/wp-config.php' (if present)");
    }
}
