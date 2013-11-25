<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | All rights reserved.                                                      |
// |                                                                           |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Rehber 1.1                                                               |
// +---------------------------------------------------------------------------+
// | UserAjaxProvider.php                                                  	   |
// +---------------------------------------------------------------------------+
// | Authors:   Siavash Habil Amirkhiz <amirkhiz@gmail.com>                    |
// +---------------------------------------------------------------------------+


/**
 * Wrapper for various ajax methods.
 *
 * @package Rehber
 */

require_once SGL_CORE_DIR . '/Delegator.php';
require_once SGL_CORE_DIR . '/AjaxProvider2.php';
include_once 'DB/DataObject.php';


class UserAjaxProvider extends SGL_AjaxProvider2
{

    public $req;
    public $responseFormat;
    public $aMsg;

    /**
     *Constructor of UserAjaxProvider class.
     */
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        parent::__construct();

        $this->req = &SGL_Registry::singleton()->getRequest();

        $this->responseFormat = SGL_RESPONSEFORMAT_HTML;
    }

	function _isOwner($requestedUsrId)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);

        return true;
    }
    
    
    function getAddr($input, $output)
    {
    	//SGL::logMessage(null, PEAR_LOG_DEBUG);
		//echo "asdasdasd"; exit;
    	//$cityId = $this->req->get('frmCity');
    	//$title = $this->req->get('frmTitle');
    	
    	/* $region = DB_DataObject::factory($this->conf['table']['region']);
    	$region->whereAdd("city_id = " . $cityId);
    	$region->find();
    	$aRegion = array();
    	while($region->fetch()){
    		$aRegion[$region->region_id] = $region->title;
    	} */
    	
    	//$output->title = $title;
    	//$output->upTitle = ucfirst($title);
    	//$output->aOptions = $aRegion;
    	//$output->data = $this->_renderTemplate($output, 'propertySelectbox.html');
    	//echo 'hello world';
    	$output->data = "asdad asd" ;
    }
}

?>