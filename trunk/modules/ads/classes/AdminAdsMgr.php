<?php
/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Copyright (c) 2010, Demian Turner                                         |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Seagull 1.0                                                               |
// +---------------------------------------------------------------------------+
// | adsMgr.php                                                    			   |
// +---------------------------------------------------------------------------+
// | Author: Siavash Habil Amirkhiz <amirkhhiz@gmail.com>                      |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
include_once SGL_MOD_DIR  . '/ads/classes/Image.php';
include_once SGL_CORE_DIR . '/Image.php';
/**
 * Type your class description here ...
 *
 * @package ads
 * @author  Siavash Habil Amirkhiz <amirkhhiz@gmail.com>
 */
class AdminAdsMgr extends SGL_Manager
{
	var $cImage;
	var $aBlocks = array(
        		0 => 'Top Slider',
        		1 => 'Left Block',
        		2 => 'Video Block',
        		3 => 'Footer Fix Block',
        		4 => 'Footer Slide Block',
				5 => 'Offers Block',
				6 => 'Right Block'
        );
	
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'AdsMgr';
        $this->template     = 'adsList.html';

        $this->_aActionsMapping =  array(
            'add'       	=> array('add'),
            'insert'    	=> array('insert', 'redirectToDefault'),
            'edit'      	=> array('edit'), 
            'update'    	=> array('update', 'redirectToDefault'),
            'list'      	=> array('list'),
            'delete'    	=> array('delete', 'redirectToDefault'),
        );
    }

    function validate($req, &$input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'list';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->ads 		= (object)$req->get('ads');
        $input->adsId 		= $req->get('frmAdsID');
        $input->aImage 		= $req->get('aImage');
        
        $this->cImage = Image::singleton();
        
    }

    function display($output)
    {
        if ($this->conf['AdsMgr']['showUntranslated'] == false) {
            $c = SGL_Config::singleton();
            $c->set('debug', array('showUntranslated' => false));
        }
    }
    
	function _editDisplay($input, $output)
	{
		/* 
		 * Find Users From the user table to fetch in select box
		 *  */
		$user = DB_DataObject::factory($this->conf['table']['user']);
		$user->selectAdd();
		$user->selectAdd('usr_id, username');
		$user->whereAdd('role_id = 2');
		$user->find();
		
		$aUsers = array();
		while ($user->fetch()) {
			$aUsers[$user->usr_id] = $user->username;
		}
		$output->aUsers = $aUsers;
		
		$output->aBlocks = $this->aBlocks; 
		
	}

    function _cmd_add(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'adsEdit.html';
        $output->pageTitle = 'AdsMgr :: Add';
        $output->action    = 'insert';
        $output->wysiwyg   = true;
        
        $this->_editDisplay($input, $output);
    }

    function _cmd_insert(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        
        
        $ads = DB_DataObject::factory($this->conf['table']['ads']);
        $ads->setFrom($input->ads);
        $ads->ads_id = $this->dbh->nextId($this->conf['table']['ads']);
        $ads->start_date = $this->dbh->quote($input->start_date);
        $ads->end_date = $this->dbh->quote($input->end_date);

        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
	        $input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
	        $this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
	        $ads->image = $input->aImage['name'];
        }
        //echo "<pre>"; print_r($input->ads); echo "</pre>";die;
        
        $success = $ads->insert();

        if ($success !== false) {
            SGL::raiseMsg('ads insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError("ads insert NOT successfull",
                SGL_ERROR_NOAFFECTEDROWS);
        }
    }

    function _cmd_edit(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'adsEdit.html';
        $output->pageTitle = 'AdsMgr :: Edit';
        $output->action    = 'update';
        $output->wysiwyg   = true;

        $ads = DB_DataObject::factory($this->conf['table']['ads']);
        $ads->get($input->adsId);
        $output->ads = $ads;
        
        $this->_editDisplay($input, $output);
    }

    function _cmd_update(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $ads = DB_DataObject::factory($this->conf['table']['ads']);
        $ads->ads_id = $input->adsId;
        $ads->find(true);
        $ads->setFrom($input->ads);
        
        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
        	$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
        	$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
        	$ads->image = $input->aImage['name'];
        }
        
        $success = $ads->update();

        if ($success !== false) {
            SGL::raiseMsg('ads update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('ads update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }    
    }

    function _cmd_list(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'adsList.html';
        $output->pageTitle = 'AdsMgr :: List';

        $query = "
            	SELECT
                  	*
                FROM {$this->conf['table']['ads']} AS a
				LEFT JOIN {$this->conf['table']['user']} AS usr
					ON usr.usr_id = a.usr_id
			";

		$limit = $_SESSION['aPrefs']['resPerPage'];
        $pagerOptions = array(
        	'mode'      => 'Sliding',
            'delta'     => 8,
            'perPage'   => 10, 
		);
        $aPagedData = SGL_DB::getPagedData($this->dbh, $query, $pagerOptions);
        if (PEAR::isError($aPagedData)) {
        	return false;
		}
            
        //set block position name
        foreach ($aPagedData['data'] as $key => $value)
        	$aPagedData['data'][$key]['block'] = $this->aBlocks[$value['block_id']];
            
		$output->aPagedData = $aPagedData;
        $output->totalItems = $aPagedData['totalItems'];

        if (is_array($aPagedData['data']) && count($aPagedData['data'])) {
        	$output->pager = ($aPagedData['totalItems'] <= $limit) ? false : true;
		}
        //echo "<pre>"; print_r($aPagedData['data']); echo "</pre>";die;
    }

    function _cmd_delete(&$input, &$output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        if (is_array($input->aDelete)) {
            foreach ($input->aDelete as $index => $adsId) {
                $ads = DB_DataObject::factory($this->conf['table']['ads']);
                $ads->get($adsId);
                $ads->delete();
                unset($ads);
            }
            SGL::raiseMsg('ads delete successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('ads delete NOT successfull ' .
                __CLASS__ . '::' . __FUNCTION__, SGL_ERROR_INVALIDARGS);
        }    
    }

}
?>