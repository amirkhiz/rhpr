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
// | branchMgr.php                                                    		   |
// +---------------------------------------------------------------------------+
// | Author: Siavash Habil Amirkhiz <amirkhiz@gmail.com>                       |
// +---------------------------------------------------------------------------+
// $Id: ManagerTemplate.html,v 1.2 2005/04/17 02:15:02 demian Exp $

require_once 'DB/DataObject.php';
require_once SGL_MOD_DIR . '/user/classes/UserDAO.php';
include_once SGL_MOD_DIR  . '/company/classes/CompanyImage.php';

/**
 * Type your class description here ...
 *
 * @package branch
 * @author  Siavash Habil Amirkhiz <amirkhiz@gmail.com>
 */
class BranchMgr extends SGL_Manager
{
	var $cImage;
	
    function __construct()
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        parent::__construct();

        $this->pageTitle    = 'Branch Manager';
        $this->template     = 'branchList.html';
        $this->cImage = CompanyImage::singleton();

        $this->_aActionsMapping =  array(
            'add'         => array('add'),
            'insert'      => array('insert', 'redirectToDefault'),
            'edit'        => array('edit'), 
            'update'      => array('update', 'redirectToDefault'),
        );
    }

    function validate($req, $input)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $this->validated    = true;
        $input->error       = array();
        $input->pageTitle   = $this->pageTitle;
        $input->masterTemplate = $this->masterTemplate;
        $input->template    = $this->template;
        $input->action      = ($req->get('action')) ? $req->get('action') : 'add';
        $input->aDelete     = $req->get('frmDelete');
        $input->submitted   = $req->get('submitted');
        $input->branch = (object)$req->get('branch');
        $input->branchId = $req->get('frmBranchID');
        $input->companyId = $req->get('frmCompanyID');
        $input->aImage 		= $req->get('fImage');
        $input->branch      = (object)$req->get('branch');
        
        //  if errors have occured
        if (isset($aErrors) && count($aErrors)) {
            SGL::raiseMsg('Please fill in the indicated fields');
            $input->template = 'branchEdit.html';
            $input->error = $aErrors;
            $this->validated = false;
        	    
        	if ($input->action == 'insert') {
                $input->pageTitle .= ' :: Add';
            } elseif ($input->action == 'update') {
                $input->pageTitle .= ' :: Edit';
            }
        }
    }

    function display($output)
    {
    	//  set flag to we can share add/edit templates
    	if ($output->action == 'add' || $output->action == 'insert') {
    		$output->isAdd = true;
    	}
    	
    	$city = DB_DataObject::factory($this->conf['table']['city']);
    	$city->whereAdd('status = 1');
    	$city->find();
    	$aCity = array();
    	while ($city->fetch())
    	{
    		$aCity[$city->city_id] = $city->title;
    	}
   		$output->aCity = $aCity;
   		
   		$query = "
   				SELECT 
    				c.city_id AS cId, c.Title AS cTitle, 
    				r.region_id AS rId, r.Title AS rTitle,
    				v.village_id AS vId, v.Title AS vTitle
   				FROM {$this->conf['table']['city']} AS c
   				RIGHT JOIN {$this->conf['table']['region']} AS r
   				ON r.city_id = c.city_id
   				RIGHT JOIN {$this->conf['table']['village']} AS v
   				ON v.region_id = r.region_id
    			WHERE c.city_id = {$output->branch->city_id}
    		";
    		
    	$lists = $this->dbh->getAll($query);
    	$aRegion = array();
   		$aVillage = array();
    	foreach ($lists as $key => $value)
    	{
    		$aRegion[$value->rId] = $value->rTitle;
			$aVillage[$value->vId] = $value->vTitle;
   		}
    	$output->aRegion = $aRegion;
    	$output->aVillage = $aVillage;
        
        //echo '<pre>';print_r($aVillage);echo '</pre>';die;
         
        $output->aCats = array('cat1', 'cat2', 'cat3');
    }


    function _cmd_add($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'branchEdit.html';
        
        $oCompany = DB_DataObject::factory($this->conf['table']['company']);
        $oCompany->get($input->companyId);
        $output->pageTitle = $oCompany->name . '  ' . SGL_String::translate('Branchs');
        $output->action    = 'insert';
        $output->companyId = $input->companyId;
        //echo '<pre>';print_r($oCompany);echo '</pre>';die;
    }

    function _cmd_insert($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        
        $oBranch = DB_DataObject::factory($this->conf['table']['branch']);
        $oBranch->setFrom($input->branch);
        $oBranch->branch_id = $this->dbh->nextId($this->conf['table']['branch']);
        $oBranch->date_created = $oBranch->last_updated = SGL_Date::getTime();
        $oBranch->telephone_1 = (isset($input->branch->telephone_1)) ? str_replace(' ', '', $input->branch->telephone_1) : 0;
        $oBranch->telephone_2 = (isset($input->branch->telephone_2)) ? str_replace(' ', '', $input->branch->telephone_2) : 0;
        $oBranch->fax = (isset($input->branch->fax)) ? str_replace(' ', '', $input->branch->fax) : 0;
        $oBranch->mobile = str_replace(' ', '', $input->branch->mobile);
        
        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
        	$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
        	$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
        	$oBranch->image = $input->aImage['name'];
        }
        
        $success = $oBranch->insert();
        
        if ($success !== false) {
            SGL::raiseMsg('branch insert successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('branch insert NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
	        
        SGL_HTTP::redirect(array('moduleName' => 'company', 'managerName' => 'company'));
    }

    function _cmd_edit($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $output->template  = 'branchEdit.html';
        
        $branch = DB_DataObject::factory($this->conf['table']['branch']);
        $branch->get($input->branchId);
        $output->branch = $branch;
        $output->companyId = $branch->company_id;
        
        $oCompany = DB_DataObject::factory($this->conf['table']['company']);
        $oCompany->get($branch->company_id);
        $output->pageTitle = $oCompany->name . '  ' . SGL_String::translate('Branchs');
        
        $output->action    = 'update';
        $output->wysiwyg   = true;
        
        $branch = DB_DataObject::factory($this->conf['table']['branch']);
        $branch->get($input->branchId);
        $output->branch = $branch;
        //echo '<pre>';print_r($input->branchId);echo '</pre>';die;
    }

    function _cmd_update($input, $output)
    {
        SGL::logMessage(null, PEAR_LOG_DEBUG);
        $oBranch = DB_DataObject::factory($this->conf['table']['branch']);
        $oBranch->branch_id = $input->branchId;
        $oBranch->find(true);
        $oBranch->setFrom($input->branch);
        
        $oBranch->has_branch = (isset($input->branch->has_branch)) ? 1 : 0;
        $oBranch->telephone_1 = (isset($input->branch->telephone_1)) ? str_replace(' ', '', $input->branch->telephone_1) : 0;
        $oBranch->telephone_2 = (isset($input->branch->telephone_2)) ? str_replace(' ', '', $input->branch->telephone_2) : 0;
        $oBranch->fax = (isset($input->branch->fax)) ? str_replace(' ', '', $input->branch->fax) : 0;
        $oBranch->mobile = str_replace(' ', '', $input->branch->mobile);
        $oBranch->last_updated = SGL_Date::getTime();
        
        if(isset($input->aImage['name']) && $input->aImage['name'] != "") {
        	$input->aImage['name'] = $this->cImage->generateUniqueFileName($input->aImage['name']);
        	$this->cImage->uploadImage($input->aImage['name'], $input->aImage['tmp_name']);
        	$oBranch->image = $input->aImage['name'];
        }
        if(isset($input->aLogo['name']) && $input->aLogo['name'] != "") {
        	$input->aLogo['name'] = $this->cImage->generateUniqueFileName($input->aLogo['name']);
        	$this->cImage->uploadImage($input->aLogo['name'], $input->aLogo['tmp_name']);
        	$oBranch->logo = $input->aLogo['name'];
        }
        
        $success = $oBranch->update();

        if ($success !== false) {
            SGL::raiseMsg('branch update successfull', false, SGL_MESSAGE_INFO);
        } else {
            SGL::raiseError('branch update NOT successfull',
                SGL_ERROR_NOAFFECTEDROWS);
        }
        SGL_HTTP::redirect(array('moduleName' => 'company', 'managerName' => 'company'));
    }
}
?>