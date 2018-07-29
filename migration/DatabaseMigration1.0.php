
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
            $CPNL         = new DUPX_cPanel_Controller();

            $cpnlToken    = $this->CPNL->create_token($host, $user, $pass);
            $cpnlHost     = $this->CPNL->connect($cpnlToken);

            $list_dbs     = $CPNL->list_dbs($cpnlToken);

            if ($list_dbs['status'] == true) {
                foreach ($list_dbs['api'] as $dbs) {
                    DUPX_Log::log($dbs . ' : ');
                }
                // $result = $CPNL->create_db($cpnlToken, $dbname);
                // if ($result['status'] != 1) {
                //     DUPX_Log::error('Cpanel Information has a wrong data(Create DB): ' . $result['status']);
                // }
            } else {
                DUPX_Log::error('Cpanel infromation is wrong: ' . $list_dbs['status']);
            }
        }
    }
}